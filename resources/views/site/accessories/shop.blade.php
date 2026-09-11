@extends('site.layout')

@section('title', 'Auto Accessories Shop')
@section('description', 'Shop quality accessories for your auto rickshaw with all-India delivery and secure payment.')

@section('content')

@php
    // Everything the client-side filter needs, flattened for Alpine.
    $filterItems = collect($products)->map(fn ($p) => [
        'id' => $p['id'],
        'category' => $p['category'],
        'brand' => $p['brand'],
        'price' => $p['price'],
        'rating' => $p['rating'],
        'popularity' => $p['popularity'],
    ])->values();
@endphp

<div class="ab-container py-4">
    <x-ui.breadcrumb :items="[
        ['label' => 'Home', 'href' => route('site.home')],
        ['label' => 'Accessories Shop'],
    ]" />
</div>

{{-- ==================================================================== hero --}}
<section class="relative overflow-hidden bg-ink text-white">
    <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/95 to-ink/50" aria-hidden="true"></div>

    <div class="ab-container relative py-9">
        <div class="grid gap-6 lg:grid-cols-12 lg:items-center">
            <div class="lg:col-span-6">
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
                    Auto Accessories <span class="text-accent-500">Shop</span>
                </h1>
                <p class="mt-1.5 text-sm text-white/80">Upgrade your ride with quality accessories</p>

                <ul class="mt-5 flex flex-wrap gap-x-6 gap-y-2.5">
                    @foreach ([['truck', 'All India Delivery'], ['shield', 'Secure Payment'], ['refresh', 'Easy Returns'], ['check-circle', 'Trusted Quality']] as [$icon, $label])
                        <li class="flex items-center gap-2 text-xs font-semibold">
                            <x-ui.icon :name="$icon" :size="17" class="text-accent-500" />
                            {{ $label }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                <img src="{{ asset('assets/image/auto_brands/tvs.png') }}" alt="" aria-hidden="true"
                     class="mx-auto w-full max-w-56" loading="lazy">
                <p class="ab-script text-center text-lg leading-tight text-accent-500">
                    Style Comfort Safety<br>Always with You!
                </p>
            </div>

            <div class="lg:col-span-3">
                <div class="rounded-xl bg-accent-50 p-4 text-ink">
                    <p class="flex items-center gap-2 text-sm font-extrabold">
                        <x-ui.icon name="box" :size="18" class="text-brand-500" />
                        Now Delivering Across India
                    </p>
                    <p class="mt-1 text-[11px] text-muted">
                        Quality accessories for a better driving experience
                    </p>
                    <ul class="mt-2.5 space-y-1.5">
                        @foreach (['Wide Range', 'Fast Delivery (via Shiprocket)', 'Secure Packaging'] as $point)
                            <li class="flex items-center gap-2 text-[11px]">
                                <x-ui.icon name="check" :size="12" class="text-success" /> {{ $point }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<div x-data="filterRail({
        items: {{ Js::from($filterItems) }},
        groups: ['category', 'brand'],
        preselect: {{ Js::from($activeCategory !== 'all' ? ['category' => [$activeCategory]] : []) }},
        minPrice: 0,
        maxPrice: 5000,
     })">

    {{-- ==================================================== category chip bar --}}
    <section class="border-b border-line bg-surface">
        <div class="ab-container py-4">
            <ul class="ab-scroll-x">
                @foreach ($categories as $category)
                    <li class="shrink-0">
                        <button type="button"
                                @click="clearAll(); @js($category['slug']) !== 'all' && toggle('category', @js($category['slug']))"
                                :class="(@js($category['slug']) === 'all' ? selected.category.length === 0 : isChecked('category', @js($category['slug'])))
                                    ? 'border-brand-500 bg-brand-500 text-white'
                                    : 'border-line bg-surface hover:border-brand-400'"
                                class="flex w-24 flex-col items-center gap-1.5 rounded-xl border px-2 py-3 text-center transition-colors">
                            <x-ui.product-art :art="$category['art']" class="h-9 w-auto" />
                            <span class="text-[10px] font-semibold leading-tight">{{ $category['name'] }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="ab-container py-6">
        <div class="grid gap-6 lg:grid-cols-12">

            {{-- ======================================================= filters --}}
            <aside class="lg:col-span-2">
                <button type="button" @click="mobileOpen = !mobileOpen" class="ab-btn ab-btn-ghost mb-3 w-full lg:hidden">
                    <x-ui.icon name="filter" :size="16" /> Filter Products
                    <span x-show="activeCount > 0" x-text="`(${activeCount})`" class="font-bold text-brand-500"></span>
                </button>

                <div :class="mobileOpen ? 'block' : 'hidden lg:block'" class="hidden lg:block">
                    <div class="ab-card p-4">
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="text-sm font-bold">Filter Products</h2>
                            <button type="button" @click="clearAll()"
                                    class="text-[11px] font-semibold text-brand-500 underline underline-offset-2">
                                Clear All
                            </button>
                        </div>

                        <fieldset class="mb-4">
                            <legend class="ab-label">Category</legend>
                            @foreach (collect($categories)->whereNotIn('slug', ['all', 'spare-parts']) as $category)
                                <label class="flex items-center justify-between gap-2 py-1 text-xs">
                                    <span class="flex items-center gap-2">
                                        <input type="checkbox"
                                               :checked="isChecked('category', @js($category['slug']))"
                                               @change="toggle('category', @js($category['slug']))"
                                               class="h-3.5 w-3.5 rounded border-line text-brand-500 focus:ring-brand-400">
                                        {{ $category['name'] }}
                                    </span>
                                    <span class="text-muted">({{ $category['count'] }})</span>
                                </label>
                            @endforeach
                        </fieldset>

                        <div class="mb-4">
                            <span class="ab-label">Price Range</span>
                            <input type="range" min="0" max="5000" step="100"
                                   x-model.number="maxPrice" @input="onMaxChange()"
                                   class="w-full accent-brand-500" aria-label="Maximum price">
                            <p class="mt-1 text-xs font-semibold">
                                ₹<span x-text="minPrice"></span> – ₹<span x-text="maxPrice"></span>
                            </p>
                        </div>

                        <fieldset>
                            <legend class="ab-label">Brand</legend>
                            @foreach ($brands as $brand)
                                <label class="flex items-center justify-between gap-2 py-1 text-xs">
                                    <span class="flex items-center gap-2">
                                        <input type="checkbox"
                                               :checked="isChecked('brand', @js($brand['name']))"
                                               @change="toggle('brand', @js($brand['name']))"
                                               class="h-3.5 w-3.5 rounded border-line text-brand-500 focus:ring-brand-400">
                                        {{ $brand['name'] }}
                                    </span>
                                    <span class="text-muted">({{ $brand['count'] }})</span>
                                </label>
                            @endforeach
                        </fieldset>
                    </div>
                </div>
            </aside>

            {{-- ================================================ product grid --}}
            <div class="lg:col-span-7">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <h2 class="text-lg font-extrabold">
                        All Accessories
                        <span class="text-sm font-normal text-muted">(<span x-text="resultCount"></span> Products)</span>
                    </h2>

                    <label class="flex items-center gap-2 text-xs">
                        <span class="text-muted">Sort by:</span>
                        <select x-model="sort" class="ab-field w-auto py-1.5 text-xs">
                            <option value="popularity">Popularity</option>
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                            
                        </select>
                    </label>
                </div>

                {{-- Cards are server-rendered; Alpine shows/hides and reorders them. --}}
                <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3" data-reveal-group>
                    @foreach ($products as $product)
                        <li data-reveal
                            x-show="results.some(r => r.id === @js($product['id']))"
                            :style="`order: ${results.findIndex(r => r.id === @js($product['id']))}`">
                            <x-ui.product-card :product="$product" class="h-full" />
                        </li>
                    @endforeach
                </ul>

                <p x-show="resultCount === 0" x-cloak
                   class="ab-card p-8 text-center text-sm text-muted">
                    No products match these filters.
                    <button type="button" @click="clearAll()"
                            class="font-semibold text-brand-500 underline underline-offset-2">Clear all filters</button>
                </p>
            </div>

            {{-- ========================================================= aside --}}
            <aside class="space-y-4 lg:col-span-3">
                <div class="ab-card overflow-hidden">
                    <h2 class="bg-brand-500 px-4 py-3 text-sm font-bold text-white">
                        Why Shop with AutoBazaar?
                    </h2>
                    <ul class="space-y-3 p-4">
                        @foreach ([
                            ['truck', 'All India Delivery', 'via Shiprocket'],
                            ['shield', 'Secure Online Payment', null],
                            ['refresh', 'Easy Returns & Support', null],
                            ['check-circle', 'Quality Accessories', null],
                            ['rupee', 'Best Prices', null],
                            ['headset', 'Dedicated Customer Support', null],
                        ] as [$icon, $title, $note])
                            <li class="flex items-start gap-2.5">
                                <x-ui.icon :name="$icon" :size="17" class="mt-0.5 shrink-0 text-brand-500" />
                                <span class="leading-tight">
                                    <span class="block text-xs font-semibold">{{ $title }}</span>
                                    @if ($note)<span class="block text-[10px] text-muted">{{ $note }}</span>@endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="ab-card bg-accent-50 p-4">
                    <p class="text-sm font-extrabold leading-snug">Make Every Ride More Comfortable</p>
                    <p class="mt-1 text-[11px] text-muted">Premium accessories for your auto rickshaw</p>
                    <a href="{{ route('site.accessories') }}" class="ab-btn ab-btn-accent mt-3 px-4 py-2 text-xs">
                        Shop Now <x-ui.icon name="arrow-right" :size="13" />
                    </a>
                </div>

                <div class="ab-card flex items-center gap-3 p-4">
                    <x-ui.icon name="truck" :size="22" class="text-violet-600" />
                    <span class="leading-tight">
                        <span class="block text-xs font-bold">Powered by Shiprocket</span>
                        <span class="block text-[10px] text-muted">Fast, reliable delivery across India</span>
                    </span>
                </div>
            </aside>
        </div>
    </section>
</div>

<x-ui.shop-promises :promises="$site['shop_promises']" />

@endsection
