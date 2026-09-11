@extends('site.layout')

@section('title', $title)
@section('description', $lede)

@section('content')

@php
    // Flattened shape the client-side filter matches against.
    $filterItems = collect($vehicles)->map(fn ($v) => [
        'id' => $v['slug'],
        'brand' => $v['brand'],
        'fuels' => collect($v['variants'])->pluck('label')->values(),
        'seating' => $v['seating'],
        'use_case' => $v['use_case'],
        'price' => $v['from_price'],
        'rating' => $v['rating'],
        'popularity' => $v['popularity'],
    ])->values();
@endphp

<x-ui.page-hero :title="$title" :lede="$lede" :breadcrumb="[
    ['label' => 'Home', 'href' => route('site.home')],
    ['label' => 'New Autos', 'href' => route('site.new-autos')],
    ...(isset($brand) ? [['label' => $brand['brand']]] : []),
]" />

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
            {{-- Brand strip: quick hop between makers --}}
            @unless (isset($brand))
                <ul class="ab-scroll-x mb-6">
                    @foreach (collect($vehicles)->unique('brand_slug') as $v)
                        <li class="shrink-0">
                            <a href="{{ route('site.brand', $v['brand_slug']) }}"
                               class="ab-card flex w-28 flex-col items-center gap-2 px-3 py-3 ab-lift">
                                <img src="{{ asset($v['brand_logo']) }}" alt="{{ $v['brand'] }}"
                                     class="h-6 w-auto object-contain" loading="lazy">
                                <span class="text-[11px] font-semibold">{{ $v['brand'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endunless

            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <p class="text-sm text-muted">
                    Showing <span class="font-bold text-ink" x-text="resultCount">{{ count($vehicles) }}</span> models
                </p>

                <label class="flex items-center gap-2 text-xs">
                    <span class="text-muted">Sort by:</span>
                    <select x-model="sort" class="ab-field w-auto py-1.5 text-xs">
                        <option value="popularity">Popularity</option>
                        <option value="price-asc">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                        <option value="rating">Customer Rating</option>
                    </select>
                </label>
            </div>

            {{-- Cards render server-side; Alpine shows, hides and reorders them. --}}
            <ul class="grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4" data-reveal-group>
                @foreach ($vehicles as $vehicle)
                    <li data-reveal
                        x-show="results.some(r => r.id === @js($vehicle['slug']))"
                        :style="`order: ${results.findIndex(r => r.id === @js($vehicle['slug']))}`">
                        <x-ui.vehicle-card :vehicle="$vehicle" class="h-full" />
                    </li>
                @endforeach
            </ul>

            <p x-show="resultCount === 0" x-cloak class="ab-card p-8 text-center text-sm text-muted">
                No models match these filters.
                <button type="button" @click="clearAll()"
                        class="font-semibold text-brand-500 underline underline-offset-2">Clear all filters</button>
            </p>
        </div>
    </div>
</section>

@endsection
