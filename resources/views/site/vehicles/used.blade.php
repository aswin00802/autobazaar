@extends('site.layout')

@section('title', $title)
@section('description', $lede)

@section('content')

@php
    // Used listings are a different shape from the catalogue: each row is one
    // physical vehicle with year, km and document status rather than a model
    // with variants. Derived here from the catalogue purely for the prototype.
    $listings = collect($vehicles)->flatMap(function ($v, $i) {
        return collect([0, 1])->map(fn ($n) => [
            'vehicle' => $v,
            'id' => $v['slug'] . '-' . $n,
            'year' => 2024 - (($i + $n) % 5),
            'km' => number_format(18000 + (($i + 1) * ($n + 1) * 7350)),
            'owner' => ['1st Owner', '2nd Owner'][($i + $n) % 2],
            'price' => (int) round($v['from_price'] * (0.58 + (($i + $n) % 4) * 0.05)),
            'city' => ['Chennai', 'Tiruvallur', 'Kanchipuram', 'Chengalpattu'][($i + $n) % 4],
            'fc' => ['Valid', 'Valid', 'Expired'][($i + $n) % 3],
            'permit' => 'Valid',
        ]);
    })->take(8);
@endphp

<x-ui.page-hero :title="$title" :lede="$lede" :breadcrumb="[
    ['label' => 'Home', 'href' => route('site.home')],
    ['label' => 'Used Autos'],
]" />

@php
    $filterItems = $listings->map(fn ($l) => [
        'id' => $l['id'],
        'brand' => $l['vehicle']['brand'],
        'fuels' => collect($l['vehicle']['variants'])->pluck('label')->values(),
        'seating' => $l['vehicle']['seating'],
        'use_case' => $l['vehicle']['use_case'],
        'price' => $l['price'],
        'rating' => $l['vehicle']['rating'],
        'popularity' => $l['vehicle']['popularity'],
    ])->values();
@endphp

<section class="ab-container py-8"
         x-data="filterRail({
            items: {{ Js::from($filterItems) }},
            groups: ['brand', 'fuels', 'seating', 'use_case'],
            minPrice: 100000,
            maxPrice: 500000,
         })">
    <div class="grid gap-6 lg:grid-cols-12">

        <aside class="lg:col-span-2">
            <x-ui.vehicle-filters :vehicles="$vehicles" />
        </aside>

        <div class="lg:col-span-10">
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-muted">
                    Showing <span class="font-bold text-ink" x-text="resultCount">{{ $listings->count() }}</span>
                    verified listings
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
                    @php $v = $listing['vehicle']; @endphp
                    <li x-show="results.some(r => r.id === @js($listing['id']))"
                        :style="`order: ${results.findIndex(r => r.id === @js($listing['id']))}`"
                        class="ab-card flex h-full flex-col overflow-hidden ab-lift">

                        <div class="relative bg-canvas p-4">
                            <span class="absolute left-3 top-3 rounded bg-brand-500 px-2 py-0.5 text-[10px] font-bold text-white">
                                {{ $listing['year'] }}
                            </span>
                            <img src="{{ asset($v['image']) }}" alt="{{ $v['name'] }}"
                                 class="mx-auto h-28 w-auto object-contain" loading="lazy">
                        </div>

                        <div class="flex flex-1 flex-col p-4">
                            <h2 class="text-sm font-bold">{{ $v['name'] }}</h2>
                            <p class="text-[11px] text-muted">
                                {{ $listing['km'] }} km · {{ $listing['owner'] }} · {{ $listing['city'] }}
                            </p>

                            <p class="mt-2 text-lg font-extrabold">₹{{ number_format($listing['price']) }}</p>

                            {{-- Document status: what actually matters on a used auto --}}
                            <ul class="mt-3 flex flex-wrap gap-1.5">
                                @foreach ([['RC', 'Valid'], ['FC', $listing['fc']], ['Permit', $listing['permit']]] as [$doc, $status])
                                    <li class="rounded px-2 py-0.5 text-[10px] font-semibold
                                               {{ $status === 'Valid' ? 'bg-brand-50 text-brand-600' : 'bg-red-50 text-danger' }}">
                                        {{ $doc }}: {{ $status }}
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-auto flex gap-2 pt-4">
                                <a href="{{ route('site.model', [$v['brand_slug'], $v['model_slug']]) }}"
                                   class="ab-btn ab-btn-primary flex-1 px-2 py-2 text-xs">View Details</a>
                                <a href="{{ route('site.enquiry', $v['slug']) }}"
                                   class="ab-btn ab-btn-outline flex-1 px-2 py-2 text-xs">Enquire</a>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>

@endsection
