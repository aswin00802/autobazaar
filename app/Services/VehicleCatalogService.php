<?php

namespace App\Services;

use App\Models\FinanceLenderRate;
use App\Models\Masters\AutoFuelType;
use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleOffer;
use Illuminate\Database\Eloquent\Collection;

/**
 * Reads the vehicle catalogue and shapes it for the website and the app.
 *
 * `detail()` returns the SAME array shape the fixture file used
 * (resources/fixtures/vehicles.php) so every existing component keeps
 * working, plus the extra sections the redesigned detail page needs
 * (finance, stock, warranty, showroom, documents, review list).
 *
 * Both the web controller and GET /api/v1/vehicles/{slug} go through here —
 * one place computes price, EMI and running cost, never the frontend.
 */
class VehicleCatalogService
{
    /** Defaults for the "EMI from" line and compare table. Settings later. */
    public const EMI_DOWN_PAYMENT_PCT = 20;
    public const EMI_DEFAULT_RATE = 9.5;
    public const EMI_DEFAULT_TENURE = 36;
    public const RUNNING_COST_DAILY_KM = 150;
    public const COMPARE_DAILY_KM = 40;

    private ?Collection $fuelPrices = null;

    /* ------------------------------------------------------------ queries */

    /** Live models, catalogue order — for grids, home, compare, search. */
    public function all(): array
    {
        return VehicleModel::live()
            ->with(['brand', 'variants', 'prices', 'features', 'scores', 'specifications', 'suitability', 'offers'])
            ->orderByDesc('is_popular')->orderBy('sort_order')->orderByDesc('popularity')
            ->get()
            ->map(fn ($m) => $this->card($m))
            ->values()
            ->all();
    }

    public function popular(int $limit = 5): array
    {
        return array_slice($this->all(), 0, $limit);
    }

    public function byBrand(string $brandSlug): array
    {
        return array_values(array_filter($this->all(), fn ($v) => $v['brand_slug'] === $brandSlug));
    }

    public function findBySlug(string $slug): ?VehicleModel
    {
        return VehicleModel::live()->where('slug', $slug)->first();
    }

    public function findByBrandModel(string $brandSlug, string $modelSlug): ?VehicleModel
    {
        return VehicleModel::live()
            ->where('model_slug', $modelSlug)
            ->get()
            ->first(fn ($m) => $this->brandSlug($m) === $brandSlug);
    }

    /** Fixture-shaped array for one model, by slug (compare, enquiry). */
    public function card(VehicleModel $m): array
    {
        $m->loadMissing(['brand', 'variants', 'prices', 'features', 'scores', 'specifications', 'suitability', 'offers']);

        $variant = $this->defaultVariant($m);
        $price = $this->defaultPrice($m);
        $exShowroom = (float) ($price?->ex_showroom ?: $variant?->ex_showroom_price ?: 0);
        $onRoad = $price ? (float) $price->on_road : $exShowroom;
        $fuels = $this->operatingCostFuels($m);
        $specs = $m->specifications->map(fn ($s) => ['label' => $s->label, 'value' => $s->value])->all();
        $offers = $m->offers->filter(fn ($o) => $this->offerIsCurrent($o));

        return [
            'id' => $m->id,
            'slug' => $m->slug,
            'model_slug' => $m->model_slug,
            'name' => $m->name,
            'brand' => $this->brandLabel($m),
            'brand_slug' => $this->brandSlug($m),
            'brand_logo' => $m->brand?->profile ?: 'assets/site/autobazaar-logo.png',
            'image' => $m->image ?: 'assets/site/autobazaar-logo.png',
            'tagline' => $m->tagline,
            'badge' => $m->badge,
            'rating' => (float) $m->rating_avg,
            'reviews' => (int) $m->rating_count,
            'from_price' => $onRoad,
            'popularity' => (int) $m->popularity,
            'is_popular' => (bool) $m->is_popular,
            'seating' => (int) $m->seating,
            'use_case' => $m->use_case ?? [],
            'description' => $m->description,
            'variants' => $m->variants->map(fn ($v) => [
                'id' => $v->id,
                'key' => $v->fuel_key,
                'label' => $v->name,
                'icon' => $v->icon,
                'is_default' => (bool) $v->is_default,
                'ex_showroom' => (float) $v->ex_showroom_price,
                'mileage' => $v->mileage,
                'mileage_unit' => $v->mileage_unit,
                'engine_cc' => $v->engine_cc,
                'power' => $v->power,
                'payload_kg' => $v->payload_kg,
                'transmission' => $v->transmission,
            ])->values()->all(),
            'features' => $m->features->map(fn ($f) => ['icon' => $f->icon, 'label' => $f->label])->values()->all(),
            'specifications' => $specs,
            'key_specifications' => $m->specifications->where('is_key', 1)->map(fn ($s) => ['label' => $s->label, 'value' => $s->value])->values()->all(),
            'price_breakup' => $this->breakup($price, $exShowroom),
            'on_road_price' => $onRoad,
            'ex_showroom' => $exShowroom,
            'price_location' => $price?->location ?? 'Chennai',
            'emi_from' => $this->emi($onRoad),
            'scores' => [
                'overall' => (float) $m->score_overall,
                'rank_note' => $m->score_rank_note,
                'summary' => $m->score_summary,
                'breakdown' => $m->scores->map(fn ($s) => ['label' => $s->label, 'score' => (float) $s->score, 'tone' => $s->tone])->values()->all(),
            ],
            'suitable_for' => $m->suitability->where('type', 'suitable')->pluck('label')->values()->all(),
            'not_recommended_for' => $m->suitability->where('type', 'not_recommended')->pluck('label')->values()->all(),
            'operating_cost_fuels' => $fuels,
            'compare' => $this->compareRow($m, $variant, $exShowroom, $onRoad, $fuels),
            'offers' => $offers->pluck('title')->values()->all(),
            'offers_detail' => $offers->map(fn ($o) => [
                'title' => $o->title,
                'value_amount' => $o->value_amount,
                'valid_to' => $o->valid_to?->format('d M Y'),
            ])->values()->all(),
            'offers_worth' => (float) $offers->sum('value_amount'),
            'offers_valid_till' => $offers->whereNotNull('valid_to')->max('valid_to')?->format('d M Y'),
        ];
    }

    /** Full detail-page payload. */
    public function detail(VehicleModel $m): array
    {
        $m->loadMissing(['images', 'documents', 'stock.dealer', 'stock.variant', 'dealer', 'approvedReviews']);
        $card = $this->card($m);
        $variant = $this->defaultVariant($m);
        $fuel = collect($card['operating_cost_fuels'])->first(fn ($f) => $f['key'] === ($variant?->fuel_key ?? 'petrol'))
            ?? collect($card['operating_cost_fuels'])->first();
        $stock = $m->stock->first();
        $dealer = $stock?->dealer ?? $m->dealer;

        $images = $m->images->pluck('image')->filter()->values()->all();
        if (empty($images)) {
            $images = [$card['image']];
        }

        $running = $this->runningCost($fuel);

        return $card + [
            'images' => $images,
            'key_highlights' => $this->keyHighlights($m, $variant),
            'finance' => [
                'lenders' => $this->lenders(),
                'loan_amount' => round($card['on_road_price'] * (100 - self::EMI_DOWN_PAYMENT_PCT) / 100, -3),
                'default_tenure' => self::EMI_DEFAULT_TENURE,
            ],
            'running_cost' => $running,
            'stock' => $stock ? [
                'in_stock' => $stock->qty > 0,
                'qty' => (int) $stock->qty,
                'delivery' => $stock->delivery_days_min . ' – ' . $stock->delivery_days_max . ' Days',
                'available_at' => $dealer?->dealer_name,
                'location' => $dealer?->location,
                'colour' => $stock->colour,
            ] : null,
            'warranty' => $m->warranty_years ? [
                'vehicle' => $m->warranty_years . ' Years / ' . number_format((int) $m->warranty_km) . ' km',
                'engine' => $m->engine_warranty_years ? $m->engine_warranty_years . ' Years / ' . number_format((int) $m->engine_warranty_km) . ' km' : null,
                'service_interval' => number_format((int) $m->service_interval_km) . ' km or ' . $m->service_interval_months . ' months',
                'free_services' => $m->free_services . ' (Upto ' . number_format((int) $m->free_services_km) . ' km)',
            ] : null,
            'showroom' => $dealer ? [
                'name' => $dealer->dealer_name,
                'type' => $dealer->dealer_type,
                'address' => $dealer->address,
                'location' => $dealer->location,
                'contact' => $dealer->contact,
                'image' => $dealer->image,
                'maps_url' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode(trim($dealer->dealer_name . ' ' . $dealer->address)),
            ] : null,
            'documents' => $m->documents->map(fn ($d) => ['type' => $d->type, 'title' => $d->title, 'url' => asset($d->file)])->values()->all(),
            'review_list' => $m->approvedReviews->take(10)->map(fn ($r) => [
                'name' => $r->name,
                'city' => $r->city,
                'rating' => (int) $r->rating,
                'date' => $r->created_at?->format('d M Y'),
                'verified' => (bool) $r->is_verified,
                'title' => $r->title,
                'body' => $r->body,
            ])->values()->all(),
            'review_distribution' => $this->distribution($m),
        ];
    }

    /** Similar models: same brand first, then the rest, excluding itself. */
    public function similar(VehicleModel $m, int $limit = 4): array
    {
        $all = collect($this->all())->reject(fn ($v) => $v['slug'] === $m->slug);
        $same = $all->where('brand_slug', $this->brandSlug($m));

        return $same->concat($all->diffKeys($same))->take($limit)->values()->all();
    }

    /* ------------------------------------------------------------ pieces */

    private function defaultVariant(VehicleModel $m)
    {
        return $m->variants->firstWhere('is_default', true) ?? $m->variants->first();
    }

    private function defaultPrice(VehicleModel $m)
    {
        return $m->prices->firstWhere('is_default', true) ?? $m->prices->first();
    }

    private function breakup($price, float $exShowroom): array
    {
        if (! $price) {
            return [['label' => 'Ex-Showroom Price', 'amount' => $exShowroom]];
        }

        $rows = [
            ['label' => 'Ex-Showroom Price', 'amount' => (float) $price->ex_showroom],
            ['label' => 'RTO & Registration (' . $price->location . ')', 'amount' => (float) $price->rto],
            ['label' => 'Insurance (1 Year)', 'amount' => (float) $price->insurance],
            ['label' => 'Registration & Handling', 'amount' => (float) $price->registration],
            ['label' => 'Road Tax & Others', 'amount' => (float) $price->other],
        ];
        if ((float) $price->accessories > 0) {
            $rows[] = ['label' => 'Accessories', 'amount' => (float) $price->accessories];
        }

        return $rows;
    }

    /** Live fuel prices from auto_fuel_types, keyed by lower-case name. */
    private function fuelPrices(): Collection
    {
        return $this->fuelPrices ??= AutoFuelType::where('status', 1)->get()->keyBy(fn ($f) => strtolower($f->name));
    }

    private function operatingCostFuels(VehicleModel $m): array
    {
        $prices = $this->fuelPrices();
        $units = ['petrol' => 'km/litre', 'diesel' => 'km/litre', 'cng' => 'km/kg', 'lpg' => 'km/kg', 'electric' => 'km/unit'];

        return $m->variants
            ->filter(fn ($v) => $v->mileage)
            ->map(fn ($v) => [
                'key' => $v->fuel_key,
                'label' => $v->name,
                'price' => (float) ($prices[$v->fuel_key]->price ?? 0),
                'mileage' => (float) $v->mileage,
                'unit' => $v->mileage_unit ?: ($units[$v->fuel_key] ?? 'km/litre'),
            ])->values()->all();
    }

    /** Reducing-balance EMI — same formula as resources/js/components/emi-calculator.js. */
    public function emi(float $price, ?float $rate = null, ?int $months = null, ?float $loan = null): int
    {
        $p = $loan ?? $price * (100 - self::EMI_DOWN_PAYMENT_PCT) / 100;
        $n = $months ?? self::EMI_DEFAULT_TENURE;
        $r = ($rate ?? self::EMI_DEFAULT_RATE) / 12 / 100;
        if ($p <= 0 || $n <= 0) {
            return 0;
        }
        if ($r == 0) {
            return (int) round($p / $n);
        }
        $g = pow(1 + $r, $n);

        return (int) round($p * $r * $g / ($g - 1));
    }

    public function emiTable(float $loan, float $rate, array $tenures): array
    {
        return array_map(function ($months) use ($loan, $rate) {
            $emi = $this->emi(0, $rate, $months, $loan);

            return [
                'months' => $months,
                'years' => $months / 12,
                'emi' => $emi,
                'total_interest' => max(0, $emi * $months - $loan),
                'total_payment' => $emi * $months,
            ];
        }, $tenures);
    }

    private function runningCost(?array $fuel): ?array
    {
        if (! $fuel || $fuel['mileage'] <= 0) {
            return null;
        }
        $perKm = $fuel['price'] / $fuel['mileage'];
        $daily = $perKm * self::RUNNING_COST_DAILY_KM;

        return [
            'fuel' => $fuel['label'],
            'daily_km' => self::RUNNING_COST_DAILY_KM,
            'per_km' => round($perKm, 2),
            'daily' => (int) round($daily),
            'monthly' => (int) round($daily * 30),
            'annual' => (int) round($daily * 360),
        ];
    }

    private function compareRow(VehicleModel $m, $variant, float $exShowroom, float $onRoad, array $fuels): array
    {
        $fuelLabels = $m->variants->pluck('name')->implode(' / ');
        $mileage = collect($fuels)->map(fn ($f) => $f['mileage'] . ' ' . $f['unit'] . ' (' . $f['label'] . ')')->implode("\n");
        $default = collect($fuels)->first(fn ($f) => $f['key'] === ($variant?->fuel_key ?? '')) ?? collect($fuels)->first();
        $monthlyFuel = $default && $default['mileage'] > 0
            ? (int) round($default['price'] / $default['mileage'] * self::COMPARE_DAILY_KM * 30, -2)
            : 0;

        return [
            'engine' => $variant?->engine_cc ?: '—',
            'power' => $variant?->power ?: '—',
            'mileage' => $mileage ?: '—',
            'seating' => (string) $m->seating,
            'fuel' => $fuelLabels ?: '—',
            'ex_showroom_label' => '₹' . number_format($exShowroom / 100000, 2) . ' Lakh*',
            'emi' => $this->emi($onRoad),
            'maintenance' => $m->maintenance_level ?: 'Low',
            'best_for' => $m->best_for ?: 'All Rounder',
            'monthly_fuel' => $monthlyFuel,
            'fuel_note' => $default['label'] ?? '',
        ];
    }

    private function keyHighlights(VehicleModel $m, $variant): array
    {
        return array_values(array_filter([
            ['icon' => 'chart', 'label' => 'Brand', 'value' => $this->brandLabel($m)],
            ['icon' => 'auto', 'label' => 'Model', 'value' => trim(str_replace($this->brandLabel($m), '', $m->name)) ?: $m->name],
            ['icon' => 'fuel', 'label' => 'Fuel Type', 'value' => $variant?->name],
            ['icon' => 'gauge', 'label' => 'Mileage', 'value' => $variant?->mileage ? $variant->mileage . ' ' . $variant->mileage_unit : null],
            ['icon' => 'bolt', 'label' => 'Engine', 'value' => $variant?->engine_cc],
            ['icon' => 'users', 'label' => 'Passenger Capacity', 'value' => (string) $m->seating],
            ['icon' => 'box', 'label' => 'Payload Capacity', 'value' => $variant?->payload_kg ? '~ ' . $variant->payload_kg . ' kg' : null],
            ['icon' => 'gear', 'label' => 'Transmission', 'value' => $variant?->transmission],
        ], fn ($h) => ! empty($h['value'])));
    }

    /** Finance partners with loan terms, for the Finance Options card. */
    public function lenders(): array
    {
        return FinanceLenderRate::with('lender')
            ->where('status_id', 1)
            ->whereHas('lender', fn ($q) => $q->where('status_id', 1))
            ->orderByDesc('is_featured')->orderBy('sort_order')
            ->get()
            ->map(fn ($r) => [
                'id' => $r->auto_financiar_id,
                'name' => $r->lender->finance_name,
                'type' => $r->lender->finance_type,
                'logo' => $r->lender->image,
                'rate' => (float) $r->interest_rate,
                'tenures' => $r->tenures(),
                'max_loan_pct' => (int) $r->max_loan_pct,
                'max_loan_pct_no_cibil' => (int) $r->max_loan_pct_no_cibil,
                'processing_fee_pct' => (float) $r->processing_fee_pct,
                'documents' => array_values(array_filter(array_map('trim', explode("\n", (string) $r->documents)))),
                'contact' => $r->lender->contact,
            ])->values()->all();
    }

    private function distribution(VehicleModel $m): array
    {
        $counts = $m->approvedReviews->countBy('rating');
        $total = max(1, $m->approvedReviews->count());
        $out = [];
        foreach ([5, 4, 3, 2, 1] as $star) {
            $out[$star] = (int) round(($counts[$star] ?? 0) / $total * 100);
        }

        return $out;
    }

    private function offerIsCurrent(VehicleOffer $o): bool
    {
        $today = now()->startOfDay();

        return $o->status_id == 1
            && (! $o->valid_from || $o->valid_from->lte($today))
            && (! $o->valid_to || $o->valid_to->gte($today));
    }

    private function brandLabel(VehicleModel $m): string
    {
        $name = $m->brand?->brand_name ?? '';

        return $name === strtoupper($name) ? ucfirst(strtolower($name)) : $name;
    }

    private function brandSlug(VehicleModel $m): string
    {
        return \Illuminate\Support\Str::slug($m->brand?->brand_name ?? '');
    }
}
