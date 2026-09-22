<?php

namespace App\Services;

use App\Models\Auto\Auto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Real used-auto listings for the website.
 *
 * Source: the same `auto_posts` rows the mobile app and the admin "Used Autos"
 * screen use (auto_usage_status = used_auto, auto_status = active). Nothing is
 * written here; the website only reads.
 *
 * The rows were typed by many different people, so this class is mostly about
 * presenting them consistently:
 *   price        -> `price_expectations` holds the asking price in rupees; 0/blank = "Price on request"
 *   location     -> free text in `rto` ("redhils", "TN 05", "rto") cleaned up, or left out
 *   documents    -> "1/2027", "2027", "Expired", "Yes"... turned into Valid / Expired / Available
 *   photos       -> only files that exist and that a browser can show (HEIC uploads are skipped)
 *
 * The seller's name, phone number and full registration number are never sent
 * to the page: enquiries go to AutoBazaar, not around it.
 */
class UsedAutoService
{
    private const WEB_IMAGE_TYPES = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    /** Every live listing, newest first, as plain arrays for the views. */
    public function all(): array
    {
        return $this->query()->latest('id')->get()->map(fn (Auto $auto) => $this->present($auto))->values()->all();
    }

    /** One live listing, or null when it is sold / removed / not a used auto. */
    public function find(int $id): ?array
    {
        $auto = $this->query()->whereKey($id)->first();

        return $auto ? $this->present($auto, true) : null;
    }

    /** Other live listings of the same brand first, then anything recent. */
    public function similar(array $listing, int $limit = 4): array
    {
        return collect($this->all())
            ->reject(fn ($l) => $l['id'] === $listing['id'])
            ->sortByDesc(fn ($l) => $l['brand'] === $listing['brand'] ? 1 : 0)
            ->take($limit)->values()->all();
    }

    private function query()
    {
        return Auto::query()
            ->with(['autoBrands', 'autoModel', 'autoFueltype', 'autoOwners'])
            ->where('auto_usage_status', 'used_auto')
            ->where('auto_status', 'active');
    }

    private function present(Auto $auto, bool $full = false): array
    {
        $brand = $this->titleCase((string) ($auto->autoBrands->brand_name ?? ''));
        $model = trim((string) ($auto->specific_model ?: ($auto->autoModel->model_name ?? $auto->autoModel->name ?? '')));
        $model = trim(preg_replace('/\s*\(.*?\)\s*/', ' ', $model));           // "Ape NXT Plus (or NXT+)" -> "Ape NXT Plus"
        // some rows have a year (or nothing useful) typed in the model box
        if ($model === '' || preg_match('/^(?:19|20)\d{2}$/', $model)) {
            $model = trim((string) ($auto->autoModel->model_name ?? $auto->autoModel->name ?? ''));
        }
        $model = $this->modelCase($model);
        $name  = trim($brand . ' ' . $model) ?: 'Used Autorickshaw';

        $year  = preg_match('/\b(19|20)\d{2}\b/', (string) $auto->registration_year, $m) ? (int) $m[0] : null;
        $km    = (int) preg_replace('/\D+/', '', (string) $auto->kilometer);
        $price = (int) preg_replace('/\D+/', '', (string) $auto->price_expectations);
        $fuel  = $this->titleCase((string) ($auto->autoFueltype->name ?? ''));
        $images = $this->images($auto);

        $listing = [
            'id'        => (int) $auto->id,
            'ref'       => (string) ($auto->auto_unique_id ?: 'AB-' . $auto->id),
            'slug'      => Str::slug(trim($name . ' ' . ($year ?: ''))) ?: 'used-auto',
            'name'      => $name,
            'brand'     => $brand ?: 'Other',
            'model'     => $model,
            'year'      => $year,
            'km'        => $km,
            'km_label'  => $km > 0 ? $this->indianNumber($km) . ' km' : null,
            'owner'     => $this->ownerLabel($auto),
            'fuel'      => $fuel ?: null,
            'location'  => $this->location((string) $auto->rto),
            'price'     => $price,
            'price_label' => $price > 0 ? '₹' . $this->indianNumber($price) : 'Price on request',
            'image'     => $images[0] ?? null,
            'brand_logo' => $this->brandLogo($auto),
            'documents' => [
                'RC'     => $this->documentStatus($auto->rc_status),
                'FC'     => $this->documentStatus($auto->fc_status),
                'Permit' => $this->documentStatus($auto->permit_status),
            ],
            'posted'    => optional($auto->created_at)->timestamp ?? 0,
        ];

        if ($full) {
            $listing['images']      = $images;
            $listing['description'] = trim((string) $auto->descriptions) ?: null;
            $listing['documents']['Insurance'] = $this->documentStatus($auto->insurance);
            $listing['details'] = array_filter([
                'Registration year' => $year,
                'Kilometres driven' => $listing['km_label'],
                'Ownership'         => $listing['owner'],
                'Fuel type'         => $listing['fuel'],
                'Registered at'     => $listing['location'],
                'Registration no.'  => $this->maskRegistration((string) $auto->registration_number),
                'Seating'           => $auto->passenger_capacity ? $auto->passenger_capacity . ' passengers' : null,
                'Loan / finance'    => $this->loanLabel((string) $auto->loan_status),
                'Listing ID'        => $listing['ref'],
            ]);
        }

        return $listing;
    }

    /** Shown in place of a photo when a listing has none that a browser can display. */
    private function brandLogo(Auto $auto): ?string
    {
        $logo = trim((string) ($auto->autoBrands->profile ?? ''));

        return $logo !== '' && is_file(public_path($logo)) ? str_replace('\\', '/', $logo) : null;
    }

    /** Files that exist on this server and that a browser can display, in upload order. */
    private function images(Auto $auto): array
    {
        $images = [];

        foreach (range(1, 6) as $n) {
            $path = trim((string) $auto->{'image_' . $n});

            if ($path !== ''
                && in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), self::WEB_IMAGE_TYPES, true)
                && is_file(public_path($path))) {
                $images[] = str_replace('\\', '/', $path);
            }
        }

        return $images;
    }

    /**
     * "1/2027", "10/2026", "2027", "Expired", "Yes", "good", "" ...
     * -> ['state' => valid|expired|unknown, 'label' => text for the badge]
     */
    private function documentStatus($raw): array
    {
        $value = strtolower(trim((string) $raw));

        if ($value === '' || in_array($value, ['0', 'no', 'nil', 'nill', 'na', 'n/a', 'not available', 'year'], true)) {
            return ['state' => 'unknown', 'label' => 'Ask us'];
        }

        if (str_contains($value, 'expire')) {
            return ['state' => 'expired', 'label' => 'Expired'];
        }

        // month/year or just a year: valid until the end of that period
        if (preg_match('/^(?:(\d{1,2})\s*[\/\-.]\s*)?((?:19|20)\d{2})$/', $value, $m)) {
            $month = $m[1] !== '' && (int) $m[1] >= 1 && (int) $m[1] <= 12 ? (int) $m[1] : 12;
            $until = Carbon::create((int) $m[2], $month, 1)->endOfMonth();

            return $until->isPast()
                ? ['state' => 'expired', 'label' => 'Expired']
                : ['state' => 'valid', 'label' => 'Valid till ' . $until->format('M Y')];
        }

        if (in_array($value, ['yes', 'y', 'good', 'available', 'avilable', 'valid', 'active', 'clear', 'done', 'ok'], true)) {
            return ['state' => 'valid', 'label' => 'Available'];
        }

        return ['state' => 'unknown', 'label' => 'Ask us'];
    }

    private function ownerLabel(Auto $auto): ?string
    {
        $n = (int) preg_replace('/\D+/', '', (string) ($auto->autoOwners->name ?? $auto->owner));

        return match (true) {
            $n === 1 => '1st Owner',
            $n === 2 => '2nd Owner',
            $n === 3 => '3rd Owner',
            $n >= 4  => $n . 'th Owner',
            default  => null,
        };
    }

    /** "redhils" -> "Redhils", "TN05" / "tn 20" -> "RTO TN 05", "rto" / "" -> null */
    private function location(string $rto): ?string
    {
        $rto = trim(preg_replace('/\s+/', ' ', $rto));

        // People also type loan or document words into this box; they are not places.
        $junk = ['rto', 'na', 'n/a', 'nil', 'nill', '-', '0', 'clear', 'yes', 'no', 'good', 'available', 'avilable', 'done', 'ok', 'expired'];

        if ($rto === '' || in_array(strtolower($rto), $junk, true) || preg_match('/^\d+$/', $rto)) {
            return null;
        }

        if (preg_match('/^([a-z]{2})\s*-?\s*(\d{1,2})$/i', $rto, $m)) {
            return 'RTO ' . strtoupper($m[1]) . ' ' . str_pad($m[2], 2, '0', STR_PAD_LEFT);
        }

        return mb_strlen($rto) <= 30 ? $this->titleCase($rto) : null;
    }

    /** TN18BJ1902 -> TN 18 ** 1902 : enough to recognise the RTO, not enough to trace the owner. */
    private function maskRegistration(string $number): ?string
    {
        $clean = strtoupper(preg_replace('/[^a-z0-9]/i', '', $number));

        if (preg_match('/^([A-Z]{2})(\d{1,2})[A-Z]{0,3}(\d{4})$/', $clean, $m)) {
            return $m[1] . ' ' . $m[2] . ' ** ' . $m[3];
        }

        return null;
    }

    private function loanLabel(string $raw): ?string
    {
        $value = strtolower(trim($raw));

        return match (true) {
            $value === '' => null,
            str_contains($value, 'not') || in_array($value, ['no', 'nil', 'clear', 'bad'], true) => 'No loan on this auto',
            default => 'Finance can be arranged',
        };
    }

    /** "compact RE" -> "Compact RE"; words already in caps (RE, NXT, CNG) are left alone. */
    private function modelCase(string $text): string
    {
        $words = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return implode(' ', array_map(
            fn ($w) => preg_match('/^[A-Z0-9+.\-]{2,}$/', $w) ? $w : Str::ucfirst(mb_strtolower($w)),
            $words,
        ));
    }

    private function titleCase(string $text): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text));

        // keep short all-caps brand names (TVS) as they are; fix shouting and lower-case typing
        return $text !== '' && mb_strlen($text) <= 3 ? mb_strtoupper($text) : Str::title(mb_strtolower($text));
    }

    /** 175000 -> 1,75,000 */
    private function indianNumber(int $n): string
    {
        $s = (string) abs($n);

        if (strlen($s) > 3) {
            $s = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($s, 0, -3)) . ',' . substr($s, -3);
        }

        return $s;
    }
}
