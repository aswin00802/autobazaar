@extends('site.layout')

@section('title', $title)
@section('description', count($listings)
    ? count($listings) . ' used autorickshaws for sale at AutoBazaar: ' . collect($listings)->pluck('brand')->unique()->take(4)->implode(', ') . ' and more. Year, kilometres, owner, price and RC, FC and permit status on every listing.'
    : $lede)

@section('content')

@php
    /*
     * Real listings from App\Services\UsedAutoService (the same active used autos
     * the mobile app and the admin screen show). Each one is a single physical
     * vehicle: year, km, owner, asking price and document status.
     */
    $listings = collect($listings);

    $wa = $site['contact']['whatsapp'] ?? null;

    // The filter rail reads brand + fuel options from this shape.
    $railSource = $listings->map(fn ($l) => [
        'brand' => $l['brand'],
        'variants' => $l['fuel'] ? [['label' => $l['fuel']]] : [],
        'seating' => null,
    ])->all();

    $filterItems = $listings->map(fn ($l) => [
        'id' => 'used-' . $l['id'],
        'brand' => $l['brand'],
        'fuels' => $l['fuel'] ? [$l['fuel']] : [],
        'price' => $l['price'],
        'popularity' => $l['posted'],          // "Newest First"
    ])->values();

    // Slider top = the dearest listing rounded up to the next 50,000 (never below 3 lakh).
    $priceCeiling = max(300000, (int) (ceil(($listings->max('price') ?: 0) / 50000) * 50000));

    $docTone = [
        'valid' => 'bg-brand-50 text-brand-600',
        'expired' => 'bg-red-50 text-danger',
        'unknown' => 'bg-canvas text-muted',
    ];
@endphp

<x-ui.page-hero :title="$title" :lede="$lede" :breadcrumb="[
    ['label' => 'Home', 'href' => route('site.home')],
    ['label' => 'Used Autos'],
]" />

<style>
    /* Seller photos come in every shape, so they fill a fixed frame instead of stretching the card. */
    .ab-used-photo { position: relative; display: block; height: 176px; overflow: hidden; background: #F5F7F6; }
    .ab-used-photo img.is-photo { width: 100%; height: 100%; object-fit: cover; transition: transform .35s ease; }
    .ab-card:hover .ab-used-photo img.is-photo { transform: scale(1.05); }
    .ab-used-photo .is-logo { position: absolute; inset: 0; display: grid; place-items: center; align-content: center; gap: 8px; color: #6B7671; font-size: 11px; font-weight: 600; }
    .ab-used-photo .is-logo img { max-height: 46px; max-width: 120px; opacity: .85; }
    .ab-used-year { position: absolute; left: 12px; top: 12px; z-index: 1; padding: 2px 8px; border-radius: 4px; background: #0B5D3B; color: #fff; font-size: 10px; font-weight: 700; }
    .ab-used-empty { padding: 48px 24px; text-align: center; }
    .ab-used-empty h2 { margin: 12px 0 6px; font-size: 18px; font-weight: 800; }
    .ab-used-empty p { margin: 0 auto 18px; max-width: 440px; font-size: 14px; line-height: 1.6; color: #6B7671; }
</style>

@if ($listings->isEmpty())
    <section class="ab-container py-8">
        <div class="ab-card ab-used-empty">
            <x-ui.icon name="auto" :size="40" class="mx-auto text-brand-500" />
            <h2>No used autos listed right now</h2>
            <p>New stock arrives every week. Tell us what you are looking for and we will call you the moment a matching auto comes in.</p>
            <a href="{{ route('site.contact') }}" class="ab-btn ab-btn-primary">Tell us what you need</a>
        </div>
    </section>
@else
<section class="ab-container py-8"
         x-data="filterRail({
            items: {{ Js::from($filterItems) }},
            groups: ['brand', 'fuels'],
            minPrice: 0,
            maxPrice: {{ $priceCeiling }},
         })">
    <div class="grid gap-6 lg:grid-cols-12">

        <aside class="min-w-0 lg:col-span-2">
            <x-ui.vehicle-filters :vehicles="$railSource" :groups="['fuels', 'brand']" :price-min="0" :price-max="$priceCeiling" />
        </aside>

        <div class="min-w-0 lg:col-span-10">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-muted">
                    Showing <span class="font-bold text-ink" x-text="resultCount">{{ $listings->count() }}</span>
                    used {{ \Illuminate\Support\Str::plural('auto', $listings->count()) }}
                </p>

                <label class="flex items-center gap-2 text-xs">
                    <span class="text-muted">Sort by:</span>
                    <select x-model="sort" class="ab-field w-auto py-1.5 text-xs">
                        <option value="popularity">Newest First</option>
                        <option value="price-asc">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                    </select>
                </label>
            </div>

            <ul class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" data-reveal-group>
                @foreach ($listings as $listing)
                    @php
                        $rowId = 'used-' . $listing['id'];
                        $url = route('site.used-auto', [$listing['id'], $listing['slug']]);
                        $facts = array_filter([$listing['km_label'], $listing['owner'], $listing['location']]);
                        $waText = 'Hi AutoBazaar, I am interested in the used ' . $listing['name'] . ($listing['year'] ? ' ' . $listing['year'] : '')
                                . ' (ID ' . $listing['ref'] . ', ' . $listing['price_label'] . '). ' . $url;
                    @endphp
                    <li x-show="results.some(r => r.id === @js($rowId))"
                        :style="`order: ${results.findIndex(r => r.id === @js($rowId))}`"
                        class="ab-card flex h-full flex-col overflow-hidden ab-lift">

                        <a href="{{ $url }}" class="ab-used-photo" aria-label="View {{ $listing['name'] }}">
                            @if ($listing['year'])
                                <span class="ab-used-year">{{ $listing['year'] }}</span>
                            @endif

                            @if ($listing['image'])
                                <img src="{{ asset($listing['image']) }}" alt="Used {{ $listing['name'] }} {{ $listing['year'] }}"
                                     class="is-photo" loading="lazy" width="400" height="176">
                            @else
                                <span class="is-logo">
                                    @if ($listing['brand_logo'])
                                        <img src="{{ asset($listing['brand_logo']) }}" alt="{{ $listing['brand'] }}" loading="lazy">
                                    @else
                                        <x-ui.icon name="auto" :size="34" />
                                    @endif
                                    Photos on request
                                </span>
                            @endif
                        </a>

                        <div class="flex flex-1 flex-col p-4">
                            <h2 class="text-sm font-bold">
                                <a href="{{ $url }}" class="transition-colors hover:text-brand-500">{{ $listing['name'] }}</a>
                            </h2>
                            <p class="text-[11px] text-muted">
                                {{ implode(' · ', $facts) ?: 'Details on request' }}@if ($listing['fuel']) · {{ $listing['fuel'] }}@endif
                            </p>

                            <p class="mt-2 text-lg font-extrabold">
                                <span class="{{ $listing['price'] > 0 ? 'ab-price' : '' }}">{{ $listing['price_label'] }}</span>
                            </p>

                            {{-- Document status: what actually matters on a used auto --}}
                            <ul class="mt-3 flex flex-wrap gap-1.5">
                                @foreach ($listing['documents'] as $doc => $status)
                                    <li class="rounded px-2 py-0.5 text-[10px] font-semibold {{ $docTone[$status['state']] }}">
                                        {{ $doc }}: {{ $status['label'] }}
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-auto flex gap-2 pt-4">
                                <a href="{{ $url }}" class="ab-btn ab-btn-primary flex-1 px-2 py-2 text-xs">View Details</a>
                                @if ($wa)
                                    <a href="https://wa.me/{{ $wa }}?text={{ rawurlencode($waText) }}" target="_blank" rel="noopener"
                                       class="ab-btn ab-btn-outline flex-1 px-2 py-2 text-xs">Enquire</a>
                                @else
                                    <a href="{{ route('site.contact') }}" class="ab-btn ab-btn-outline flex-1 px-2 py-2 text-xs">Enquire</a>
                                @endif
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>

            <p x-show="resultCount === 0" x-cloak class="ab-card mt-4 p-6 text-center text-sm text-muted">
                No used autos match these filters.
                <button type="button" @click="clearAll()" class="font-semibold text-brand-500 underline">Clear filters</button>
            </p>
        </div>
    </div>
</section>
@endif

@endsection
