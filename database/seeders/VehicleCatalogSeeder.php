<?php

namespace Database\Seeders;

use App\Models\FinanceLenderRate;
use App\Models\Masters\AuthorizedSeller;
use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoFuelType;
use App\Models\Masters\AutoModel;
use App\Models\Services\Finance;
use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleReview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Seeds the vehicle catalogue.
 *
 *  1. The six fully-specified models from resources/fixtures/vehicles.php
 *     (the ones the approved design was built around) — live.
 *  2. Every other row of the `auto_models` master as a DRAFT vehicle_models
 *     row (status_id 0) with brand + name only, so admin can complete them
 *     without re-typing the list.
 *  3. Default loan terms for every active finance partner (placeholder rates,
 *     flagged for admin to correct).
 *  4. Three approved sample reviews per fixture model.
 *
 * Idempotent: re-running updates the fixture models and skips existing rows.
 */
class VehicleCatalogSeeder extends Seeder
{
    /** Fixture brand label -> auto_brands.brand_name */
    private const BRAND_MAP = [
        'TVS' => 'TVS', 'Bajaj' => 'BAJAJ', 'Piaggio' => 'PIAGGIO',
        'Mahindra' => 'MAHINDRA', 'Atul' => 'ATUL',
    ];

    /** Fixture slug -> auto_models.id (best match in the master list) */
    private const MASTER_MAP = [
        'tvs-king-deluxe' => 25, 'bajaj-re' => 1, 'piaggio-ape-xtra' => 19,
        'mahindra-treo-plus' => 61, 'mahindra-alfa' => 31, 'atul-gemini' => 50,
    ];

    /** Sample reviews attached to each fixture model (approved). */
    private const REVIEWS = [
        ['name' => 'Murugan S', 'city' => 'Tiruvallur', 'rating' => 5, 'is_verified' => 1,
         'title' => 'Excellent mileage, low running cost',
         'body' => 'Running it for eight months now on CNG. The mileage is exactly as claimed and servicing has been cheap. Very happy with the purchase.'],
        ['name' => 'Selvam K', 'city' => 'Kanchipuram', 'rating' => 4, 'is_verified' => 1,
         'title' => 'Good for city, average on highway',
         'body' => 'Perfect for daily city routes. On longer runs the pickup drops with a full load, but for my usage it is more than enough.'],
        ['name' => 'Prakash R', 'city' => 'Chengalpattu', 'rating' => 5, 'is_verified' => 0,
         'title' => 'Comfortable and reliable',
         'body' => 'Seating is comfortable for passengers and I have had no breakdowns so far. Service centre nearby which makes it easy.'],
    ];

    public function run(): void
    {
        $fixtures = require resource_path('fixtures/vehicles.php');
        $brands = AutoBrand::all()->keyBy(fn ($b) => strtoupper($b->brand_name));
        $fuels = AutoFuelType::all()->keyBy(fn ($f) => strtolower($f->name));
        $firstDealer = AuthorizedSeller::where('status_id', '!=', 3)->orderBy('id')->first();

        $seeded = [];
        foreach ($fixtures as $i => $fx) {
            $brand = $brands[strtoupper(self::BRAND_MAP[$fx['brand']] ?? $fx['brand'])] ?? null;
            if (! $brand) {
                continue;
            }

            $model = VehicleModel::updateOrCreate(['slug' => $fx['slug']], [
                'auto_brand_id' => $brand->id,
                'auto_model_id' => self::MASTER_MAP[$fx['slug']] ?? null,
                'name' => $fx['name'],
                'model_slug' => $fx['model_slug'],
                'tagline' => $fx['tagline'],
                'description' => $fx['description'],
                'badge' => $fx['badge'],
                'segment' => in_array('cargo', $fx['use_case'] ?? []) ? 'cargo' : 'passenger',
                'seating' => $fx['seating'],
                'use_case' => $fx['use_case'],
                'image' => $fx['image'],
                'maintenance_level' => $fx['compare']['maintenance'] ?? 'Low',
                'best_for' => $fx['compare']['best_for'] ?? null,
                'rating_avg' => $fx['rating'],
                'rating_count' => $fx['reviews'],
                'score_overall' => $fx['scores']['overall'],
                'score_rank_note' => $fx['scores']['rank_note'],
                'score_summary' => $fx['scores']['summary'],
                'warranty_years' => 2,
                'warranty_km' => 72000,
                'engine_warranty_years' => 2,
                'engine_warranty_km' => 72000,
                'service_interval_km' => 5000,
                'service_interval_months' => 6,
                'free_services' => 3,
                'free_services_km' => 15000,
                'dealer_id' => $this->dealerFor($brand->id) ?? $firstDealer?->id,
                'popularity' => $fx['popularity'],
                'is_popular' => (int) $fx['is_popular'],
                'sort_order' => $i + 1,
                'status_id' => 1,
            ]);
            $seeded[] = $model->id;

            // Children are rebuilt from the fixture each run.
            foreach (['variants', 'specifications', 'scores', 'features', 'suitability', 'offers', 'prices', 'images', 'stock'] as $rel) {
                $model->{$rel}()->delete();
            }

            // Variants (fuel) — mileage + unit come from operating_cost_fuels.
            $ocf = collect($fx['operating_cost_fuels'])->keyBy('key');
            $engine = collect($fx['specifications'])->firstWhere('label', 'Engine')['value'] ?? null;
            $power = collect($fx['specifications'])->firstWhere('label', 'Max Power')['value'] ?? null;
            $gear = collect($fx['specifications'])->firstWhere('label', 'Transmission')['value'] ?? null;
            $variantIds = [];
            foreach ($fx['variants'] as $vi => $v) {
                $fuelRow = $fuels[$v['key']] ?? null;
                $variant = $model->variants()->create([
                    'name' => $v['label'],
                    'fuel_key' => $v['key'],
                    'fuel_type_id' => $fuelRow?->id,
                    'icon' => $v['icon'],
                    'engine_cc' => $v['key'] === 'electric' ? null : $engine,
                    'power' => $power,
                    'mileage' => $ocf[$v['key']]['mileage'] ?? null,
                    'mileage_unit' => $ocf[$v['key']]['unit'] ?? 'km/litre',
                    'transmission' => $gear,
                    'ex_showroom_price' => $fx['ex_showroom'],
                    'is_default' => (int) ($v['is_default'] ?? false),
                    'sort_order' => $vi,
                ]);
                $variantIds[$v['key']] = $variant->id;
            }
            if (! $model->variants()->where('is_default', 1)->exists()) {
                $model->variants()->orderBy('id')->first()?->update(['is_default' => 1]);
            }

            // Specifications — first six flagged as key specs.
            foreach ($fx['specifications'] as $si => $s) {
                $model->specifications()->create([
                    'spec_group' => 'General',
                    'label' => $s['label'],
                    'value' => $s['value'],
                    'is_key' => $si < 6 ? 1 : 0,
                    'sort_order' => $si,
                ]);
            }

            foreach ($fx['scores']['breakdown'] as $si => $s) {
                $model->scores()->create(['label' => $s['label'], 'score' => $s['score'], 'tone' => $s['tone'], 'sort_order' => $si]);
            }
            foreach ($fx['features'] as $fi => $f) {
                $model->features()->create(['icon' => $f['icon'], 'label' => $f['label'], 'sort_order' => $fi]);
            }
            foreach ($fx['suitable_for'] as $si => $label) {
                $model->suitability()->create(['type' => 'suitable', 'label' => $label, 'sort_order' => $si]);
            }
            foreach ($fx['not_recommended_for'] as $si => $label) {
                $model->suitability()->create(['type' => 'not_recommended', 'label' => $label, 'sort_order' => $si]);
            }
            foreach ($fx['offers'] as $oi => $title) {
                $model->offers()->create([
                    'title' => $title,
                    'value_amount' => preg_match('/₹\s?([\d,]+)/u', $title, $m) ? (float) str_replace(',', '', $m[1]) : null,
                    'valid_to' => now()->addMonths(3)->endOfMonth()->toDateString(),
                    'sort_order' => $oi,
                ]);
            }

            // Chennai on-road breakup from the fixture rows.
            $breakup = collect($fx['price_breakup'])->keyBy(fn ($r) => Str::slug($r['label']));
            $pick = fn (string $needle) => (float) ($breakup->first(fn ($r, $k) => str_contains($k, $needle))['amount'] ?? 0);
            $model->prices()->create([
                'location' => 'Chennai',
                'state' => 'Tamil Nadu',
                'ex_showroom' => $fx['ex_showroom'],
                'rto' => $pick('rto'),
                'insurance' => $pick('insurance'),
                'registration' => $pick('registration'),
                'other' => $pick('other'),
                'accessories' => 0,
                'is_default' => 1,
            ]);

            $model->images()->create(['image' => $fx['image'], 'sort_order' => 0]);

            $model->stock()->create([
                'vehicle_variant_id' => $variantIds[array_key_first($variantIds)] ?? null,
                'dealer_id' => $model->dealer_id,
                'qty' => 2,
                'delivery_days_min' => 1,
                'delivery_days_max' => 3,
            ]);

            if (! $model->reviews()->exists()) {
                foreach (self::REVIEWS as $ri => $r) {
                    $model->reviews()->create($r + ['review_status' => 'approved', 'created_at' => now()->subDays(14 * ($ri + 1))]);
                }
            }

            // Headline rating = the approved reviews that really exist, so it cannot
            // jump the first time a review is moderated.
            $model->refreshRating();
        }

        // Every master model without a catalogue row -> draft.
        $linked = VehicleModel::whereNotNull('auto_model_id')->pluck('auto_model_id')->all();
        foreach (AutoModel::where('status_id', 1)->whereNotIn('id', $linked)->with('brand')->get() as $am) {
            if (! $am->brand) {
                continue;
            }
            $brandSlug = Str::slug($am->brand->brand_name);
            $modelSlug = Str::slug(preg_replace('/\s*\(.*\)\s*/', '', $am->model_name)) ?: Str::slug($am->model_name);
            $slug = $brandSlug . '-' . $modelSlug;
            if (VehicleModel::where('slug', $slug)->exists()) {
                $slug .= '-' . $am->id;
                $modelSlug .= '-' . $am->id;
            }
            VehicleModel::firstOrCreate(['auto_model_id' => $am->id], [
                'auto_brand_id' => $am->brand_id,
                'name' => Str::title(strtolower($am->brand->brand_name)) . ' ' . preg_replace('/\s*\(.*\)\s*/', '', $am->model_name),
                'slug' => $slug,
                'model_slug' => $modelSlug,
                'image' => null,
                'status_id' => 0,
            ]);
        }

        // Loan terms for every active finance partner (placeholders).
        foreach (Finance::where('status_id', 1)->orderBy('id')->get() as $i => $lender) {
            FinanceLenderRate::firstOrCreate(['auto_financiar_id' => $lender->id], [
                'interest_rate' => 11.50,
                'min_tenure_months' => 12,
                'max_tenure_months' => 60,
                'max_loan_pct' => 85,
                'max_loan_pct_no_cibil' => 70,
                'processing_fee_pct' => 1.00,
                'documents' => "Aadhaar card\nPAN card\nDriving licence\nBadge / permit copy\n3 months bank statement\nAddress proof\n2 passport photos",
                'is_featured' => $i === 0 ? 1 : 0,
                'sort_order' => $i,
            ]);
        }

        $this->command?->info(sprintf(
            'Vehicle catalogue: %d live, %d draft models; %d lenders with rates.',
            VehicleModel::live()->count(), VehicleModel::where('status_id', 0)->count(), FinanceLenderRate::count(),
        ));
    }

    private function dealerFor(int $brandId): ?int
    {
        return AuthorizedSeller::where('status_id', '!=', 3)->where('brand_id', $brandId)->orderBy('id')->value('id');
    }
}
