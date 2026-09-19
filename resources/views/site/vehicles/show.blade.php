@extends('site.layout')

@section('title', $vehicle['name'])
@section('description', $vehicle['description'])

@section('content')

@php
    /*
     * Vehicle detail — "Auto Details" screen (WhatsApp/finance.jpeg) on the
     * site's green/yellow system. Every figure comes from $vehicle
     * (VehicleCatalogService::detail()); nothing is hard-coded here.
     */

    // ₹2,91,425 — Indian grouping, matching formatINR() in the browser.
    $inr = function ($n): string {
        $n = (int) round((float) $n);
        $s = (string) abs($n);
        if (strlen($s) > 3) {
            $s = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($s, 0, -3)) . ',' . substr($s, -3);
        }
        return ($n < 0 ? '-' : '') . '₹' . $s;
    };

    $variants = collect($vehicle['variants']);
    $defaultVariant = $variants->firstWhere('is_default', true) ?? $variants->first();
    $images = array_values(array_map(fn ($i) => asset($i), $vehicle['images'] ?: [$vehicle['image']]));

    $running = $vehicle['running_cost'] ?? null;
    $stock = $vehicle['stock'] ?? null;
    $warranty = $vehicle['warranty'] ?? null;
    $showroom = $vehicle['showroom'] ?? null;
    $documents = $vehicle['documents'] ?? [];
    $reviewList = $vehicle['review_list'] ?? [];
    $distribution = $vehicle['review_distribution'] ?? [];
    $scores = $vehicle['scores'];
    $overall = (float) ($scores['overall'] ?? 0);

    // Compact score dial (same geometry idea as x-ui.score-panel, smaller)
    $dialR = 30;
    $dialC = 2 * M_PI * $dialR;
    $dialOffset = $dialC - $dialC * max(0, min(10, $overall)) / 10;

    $leadUrl = route('site.vehicles.lead', $vehicle['slug']);
    $reviewUrl = route('site.vehicles.review', $vehicle['slug']);
    $pageUrl = url()->current();

    $tabs = [
        ['id' => 'overview', 'label' => 'Overview', 'icon' => 'grid'],
        ['id' => 'specifications', 'label' => 'Specifications', 'icon' => 'gear'],
        ['id' => 'price-emi', 'label' => 'Price & EMI', 'icon' => 'rupee'],
        ['id' => 'running-cost', 'label' => 'Running Cost', 'icon' => 'gauge'],
        ['id' => 'offers', 'label' => 'Offers', 'icon' => 'gift'],
        ['id' => 'reviews', 'label' => 'Reviews', 'icon' => 'star'],
    ];
    if (count($images) > 1) {
        $tabs[] = ['id' => 'gallery', 'label' => 'Gallery', 'icon' => 'image'];
    }

    $scrollTo = fn (string $id) => "document.getElementById('{$id}')?.scrollIntoView({ behavior: 'smooth', block: 'start' })";
    $openLead = fn (string $source) => "window.dispatchEvent(new CustomEvent('lead-open', { detail: { source: '{$source}' } }))";
@endphp

{{-- Bottom padding keeps the fixed action bar clear of the last section --}}
<div class="ab-container pb-28 pt-4 lg:pb-32">

    <x-ui.breadcrumb :items="[
        ['label' => 'Home', 'href' => route('site.home')],
        ['label' => 'New Autos', 'href' => route('site.new-autos')],
        ['label' => $vehicle['brand'], 'href' => route('site.brand', $vehicle['brand_slug'])],
        ['label' => $vehicle['name']],
    ]" />

    {{-- ================================================================ hero --}}
    <section class="mt-4 grid gap-5 lg:grid-cols-12" aria-labelledby="vehicle-title" x-data>

        {{-- Gallery ------------------------------------------------------ --}}
        <div class="min-w-0 lg:col-span-4" data-reveal="left"
             x-data="vehicleGallery({ images: {{ Js::from($images) }}, name: @js($vehicle['name']), url: @js($pageUrl) })">

            <figure class="ab-card relative overflow-hidden">
                @if ($vehicle['badge'])
                    <span class="absolute left-0 top-4 z-10 rounded-r-lg bg-danger py-1 pl-3 pr-3.5 text-[11px] font-bold uppercase tracking-wide text-white shadow">
                        <x-ui.icon name="trophy" :size="12" class="-mt-0.5 mr-1 inline" />{{ $vehicle['badge'] }}
                    </span>
                @endif

                <img src="{{ asset($vehicle['brand_logo']) }}" alt="{{ $vehicle['brand'] }}"
                     class="absolute bottom-3 left-3 z-10 h-7 w-auto max-w-24 rounded bg-surface/90 object-contain px-1.5 py-1 shadow-sm">

                {{-- Wishlist / share / compare --}}
                <div class="absolute right-3 top-3 z-10 flex flex-col gap-1.5">
                    {{-- Wishlist is UI-only until a favourites table exists for catalogue models --}}
                    <button type="button" @click="toggleWish()" :aria-pressed="wished"
                            :class="wished ? 'bg-red-50 text-danger' : 'bg-surface/90 text-ink-soft hover:text-danger'"
                            class="grid h-9 w-9 place-items-center rounded-full shadow transition-colors"
                            :aria-label="wished ? 'Remove from wishlist' : 'Add to wishlist'">
                        <x-ui.icon name="heart" :size="18" ::class="wished && 'fill-current'" />
                    </button>
                    <button type="button" @click="share()"
                            class="grid h-9 w-9 place-items-center rounded-full bg-surface/90 text-ink-soft shadow transition-colors hover:text-brand-500"
                            aria-label="Share this vehicle">
                        <x-ui.icon name="share" :size="18" />
                    </button>
                    <button type="button"
                            @click="$store.compare.toggle({ slug: @js($vehicle['slug']), name: @js($vehicle['name']) })"
                            :aria-pressed="$store.compare.has(@js($vehicle['slug']))"
                            :class="$store.compare.has(@js($vehicle['slug'])) ? 'bg-brand-50 text-brand-600' : 'bg-surface/90 text-ink-soft hover:text-brand-500'"
                            class="grid h-9 w-9 place-items-center rounded-full shadow transition-colors"
                            :aria-label="$store.compare.has(@js($vehicle['slug'])) ? 'Remove from compare' : 'Add to compare'">
                        <x-ui.icon name="scales" :size="18" />
                    </button>
                </div>

                <span x-show="copied" x-cloak x-transition.opacity
                      class="absolute left-1/2 top-3 z-10 -translate-x-1/2 rounded-full bg-ink px-3 py-1 text-[11px] font-semibold text-white"
                      role="status">Link copied</span>

                <img :src="current" src="{{ $images[0] }}" alt="{{ $vehicle['name'] }}"
                     class="aspect-4/3 w-full bg-canvas object-contain p-6 transition-opacity duration-200">

                @if (count($images) > 1)
                    <button type="button" @click="prev()"
                            class="absolute left-2 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-full bg-surface/90 shadow transition-colors hover:bg-surface"
                            aria-label="Previous image">
                        <x-ui.icon name="chevron-left" :size="18" />
                    </button>
                    <button type="button" @click="next()"
                            class="absolute right-2 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-full bg-surface/90 shadow transition-colors hover:bg-surface"
                            aria-label="Next image">
                        <x-ui.icon name="chevron-right" :size="18" />
                    </button>
                    <span class="absolute bottom-3 right-3 z-10 rounded-full bg-ink/70 px-2 py-0.5 text-[10px] font-semibold text-white">
                        <span x-text="active + 1">1</span> / {{ count($images) }}
                    </span>
                @endif
                <figcaption class="sr-only">{{ $vehicle['name'] }} photos</figcaption>
            </figure>

            @if (count($images) > 1)
                <ul class="mt-3 grid grid-cols-5 gap-2" aria-label="Thumbnails">
                    <template x-for="(img, i) in thumbs" :key="i">
                        <li>
                            <a x-show="isLastThumb(i)" x-cloak href="#gallery"
                               class="relative block overflow-hidden rounded-lg border-2 border-line bg-canvas transition-colors hover:border-brand-300"
                               :aria-label="`View all ${images.length} photos`">
                                <img :src="img" alt="" class="aspect-4/3 w-full object-contain p-1 opacity-40">
                                <span class="absolute inset-0 grid place-content-center bg-ink/60 text-center text-[11px] font-bold leading-tight text-white">
                                    +<span x-text="extra"></span><br>Photos
                                </span>
                            </a>
                            <button x-show="!isLastThumb(i)" type="button" @click="show(i)"
                                    :class="active === i ? 'border-brand-500' : 'border-line hover:border-brand-300'"
                                    class="w-full overflow-hidden rounded-lg border-2 bg-canvas transition-colors"
                                    :aria-label="`View image ${i + 1}`" :aria-pressed="active === i">
                                <img :src="img" alt="" class="aspect-4/3 w-full object-contain p-1">
                            </button>
                        </li>
                    </template>
                </ul>
            @endif
        </div>

        {{-- Title block --------------------------------------------------- --}}
        <div class="min-w-0 lg:col-span-5" data-reveal>
            <div class="flex items-center gap-3">
                <img src="{{ asset($vehicle['brand_logo']) }}" alt="" class="h-8 w-auto max-w-24 object-contain">
                <p class="text-xs font-bold uppercase tracking-widest text-muted">{{ $vehicle['brand'] }}</p>
            </div>
            <h1 id="vehicle-title" class="mt-2 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl">{{ $vehicle['name'] }}</h1>
            @if ($vehicle['tagline'])
                <p class="mt-1 text-sm text-ink-soft">{{ $vehicle['tagline'] }}</p>
            @endif

            <div class="mt-3 flex flex-wrap items-center gap-3">
                <x-ui.rating :rating="$vehicle['rating']" :reviews="$vehicle['reviews']" :size="16" />
                <a href="#reviews" class="flex items-center gap-0.5 text-xs font-semibold text-brand-500 underline-offset-2 hover:underline">
                    Read reviews <x-ui.icon name="chevron-right" :size="14" />
                </a>
            </div>

            {{-- Chips: fuel variants · seating · mileage --}}
            <ul class="mt-4 flex flex-wrap gap-2" aria-label="Highlights">
                @foreach ($variants as $v)
                    <li class="flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-bold text-brand-600">
                        <x-ui.icon :name="$v['icon']" :size="14" /> {{ $v['label'] }}
                    </li>
                @endforeach
                @if ($vehicle['seating'])
                    <li class="flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-bold text-info">
                        <x-ui.icon name="users" :size="14" /> {{ $vehicle['seating'] }} Seater
                    </li>
                @endif
                @if ($defaultVariant && $defaultVariant['mileage'])
                    <li class="flex items-center gap-1.5 rounded-full bg-accent-50 px-3 py-1.5 text-xs font-bold text-accent-700">
                        <x-ui.icon name="gauge" :size="14" /> {{ $defaultVariant['mileage'] + 0 }} {{ $defaultVariant['mileage_unit'] }}
                    </li>
                @endif
            </ul>

            {{-- Price --}}
            <div class="mt-5">
                <p class="text-3xl font-extrabold tracking-tight text-brand-600 sm:text-4xl">{{ $inr($vehicle['on_road_price']) }}<span class="text-lg font-bold text-muted">*</span></p>
                <p class="mt-1 flex items-center gap-1.5 text-sm text-ink-soft">
                    On-road Price ({{ $vehicle['price_location'] }})
                    <span class="group relative inline-flex">
                        <button type="button" class="rounded-full text-muted focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-400"
                                aria-describedby="on-road-help" aria-label="What does on-road price include?">
                            <x-ui.icon name="info" :size="15" />
                        </button>
                        <span id="on-road-help" role="tooltip"
                              class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-1.5 w-52 -translate-x-1/2 rounded-lg bg-ink px-3 py-2 text-[11px] font-medium text-white opacity-0 shadow transition-opacity group-hover:opacity-100 group-focus-within:opacity-100">
                            Includes ex-showroom, RTO, insurance and handling. See the price breakup below.
                        </span>
                    </span>
                </p>
            </div>

            @if ($vehicle['emi_from'] > 0)
                <button type="button" @click="{{ $scrollTo('price-emi') }}"
                        class="mt-3 flex w-full items-center justify-between gap-3 rounded-lg border border-accent-200 bg-accent-50 px-4 py-2.5 text-left text-sm transition-colors hover:bg-accent-100 sm:w-auto sm:min-w-72">
                    <span>EMI from <strong class="text-base">{{ $inr($vehicle['emi_from']) }}</strong>/month*</span>
                    <x-ui.icon name="chevron-right" :size="16" class="text-ink-soft" />
                </button>
            @endif

            <div class="mt-4 grid grid-cols-2 gap-2 sm:flex sm:flex-wrap">
                <button type="button" @click="{{ $scrollTo('price-emi') }}" class="ab-btn ab-btn-primary">
                    <x-ui.icon name="calculator" :size="18" /> Calculate EMI
                </button>
                <button type="button" @click="{{ $openLead('quotation') }}" class="ab-btn ab-btn-accent">
                    <x-ui.icon name="doc" :size="18" /> Get Free Quotation
                </button>
            </div>

            @if ($vehicle['description'])
                <p class="mt-5 text-sm leading-relaxed text-ink-soft">{{ $vehicle['description'] }}</p>
            @endif
        </div>

        {{-- Why choose ---------------------------------------------------- --}}
        <aside class="min-w-0 lg:col-span-3 lg:self-start" data-reveal="right" aria-labelledby="why-choose-title">
            <div class="ab-card h-full border-brand-100 bg-brand-50/60 p-5">
                <h2 id="why-choose-title" class="flex items-center gap-2 text-base font-bold">
                    <x-ui.tone-icon icon="shield" tone="brand" :size="18" shape="circle" />
                    Why Choose This Auto?
                </h2>

                <div class="mt-4 flex items-center gap-4 rounded-xl bg-surface p-3">
                    <div class="relative h-20 w-20 shrink-0">
                        <svg viewBox="0 0 72 72" class="h-full w-full -rotate-90" aria-hidden="true">
                            <circle cx="36" cy="36" r="{{ $dialR }}" fill="none" stroke="var(--color-line)" stroke-width="6" />
                            <circle cx="36" cy="36" r="{{ $dialR }}" fill="none" stroke="var(--color-accent-500)" stroke-width="6" stroke-linecap="round"
                                    stroke-dasharray="{{ $dialC }}" stroke-dashoffset="{{ $dialOffset }}" />
                        </svg>
                        <div class="absolute inset-0 grid place-content-center text-center">
                            <span class="block text-xl font-extrabold leading-none">{{ rtrim(rtrim(number_format($overall, 1), '0'), '.') }}</span>
                            <span class="block text-[10px] text-muted">/ 10</span>
                        </div>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold">AutoBazaar Score</p>
                        @if (! empty($scores['rank_note']))
                            <p class="mt-0.5 text-xs font-semibold text-brand-600">{{ $scores['rank_note'] }}</p>
                        @endif
                        <a href="#specifications" class="mt-1 inline-block text-[11px] font-semibold text-muted underline-offset-2 hover:underline">See full breakdown</a>
                    </div>
                </div>

                @if (! empty($vehicle['features']))
                    <ul class="mt-4 space-y-2.5">
                        @foreach (array_slice($vehicle['features'], 0, 5) as $feature)
                            <li class="flex items-center gap-2.5 text-sm font-semibold">
                                <x-ui.icon name="check-circle" :size="18" class="shrink-0 text-success" />
                                {{ $feature['label'] }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($vehicle['reviews'] > 0)
                    <p class="mt-4 flex items-center justify-center gap-2 rounded-lg bg-accent-100 px-3 py-2.5 text-xs font-bold text-ink">
                        <x-ui.icon name="users" :size="16" /> Trusted by {{ number_format($vehicle['reviews']) }}+ reviews
                    </p>
                @endif
            </div>
        </aside>
    </section>

    {{-- ================================================================ tabs --}}
    <div class="mt-6">
        <x-ui.detail-tabs :tabs="$tabs" />
    </div>

    {{-- ============================================================ overview --}}
    <section id="overview" class="mt-5 grid gap-5 lg:grid-cols-12" aria-label="Overview">

        {{-- Left column --}}
        <div class="min-w-0 space-y-5 lg:col-span-4" data-reveal-group>

            {{-- Key specifications --}}
            <div class="ab-card p-5" data-reveal>
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h2 class="flex items-center gap-2 text-base font-bold">
                        <x-ui.tone-icon icon="gear" tone="brand" :size="18" shape="circle" />
                        Key Specifications
                    </h2>
                    <a href="#specifications" class="flex items-center gap-0.5 text-xs font-semibold text-brand-500 underline-offset-2 hover:underline">
                        View All <x-ui.icon name="chevron-right" :size="14" />
                    </a>
                </div>
                <dl class="grid grid-cols-2 gap-x-3 gap-y-4">
                    @foreach ($vehicle['key_highlights'] as $h)
                        <div class="flex items-start gap-2.5">
                            <x-ui.tone-icon :icon="$h['icon']" tone="brand" :size="16" shape="circle" class="shrink-0" />
                            <div class="min-w-0">
                                <dt class="text-[11px] text-muted">{{ $h['label'] }}</dt>
                                <dd class="text-sm font-bold leading-tight">{{ $h['value'] }}</dd>
                            </div>
                        </div>
                    @endforeach
                </dl>
            </div>

            {{-- Price breakup --}}
            <div class="ab-card p-5" data-reveal>
                <h2 class="mb-4 flex items-center gap-2 text-base font-bold">
                    <x-ui.tone-icon icon="rupee" tone="brand" :size="18" shape="circle" />
                    Price Breakup (On-road)
                </h2>
                <dl class="divide-y divide-line text-sm">
                    @foreach ($vehicle['price_breakup'] as $row)
                        <div class="flex items-center justify-between gap-3 py-2">
                            <dt class="text-ink-soft">{{ $row['label'] }}</dt>
                            <dd class="font-semibold tabular-nums">{{ $inr($row['amount']) }}</dd>
                        </div>
                    @endforeach
                </dl>
                <div class="mt-3 flex items-center justify-between gap-3 rounded-lg bg-accent-50 px-4 py-3">
                    <p class="text-sm font-bold">On-road Price</p>
                    <p class="text-lg font-extrabold text-brand-600 tabular-nums">{{ $inr($vehicle['on_road_price']) }}</p>
                </div>
                <p class="mt-2 text-[10px] text-muted">*{{ $vehicle['price_location'] }} prices. May vary with insurance and accessories chosen.</p>
            </div>

            {{-- Current offers --}}
            <div id="offers" class="ab-card p-5" data-reveal>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <h2 class="flex items-center gap-2 text-base font-bold">
                        <x-ui.tone-icon icon="gift" tone="danger" :size="18" shape="circle" />
                        Current Offers
                    </h2>
                    @if ($vehicle['offers_valid_till'])
                        <span class="rounded-full bg-brand-50 px-2.5 py-1 text-[11px] font-bold text-brand-600">
                            Valid till {{ $vehicle['offers_valid_till'] }}
                        </span>
                    @endif
                </div>

                @if (! empty($vehicle['offers_detail']))
                    <ul class="grid gap-2 sm:grid-cols-2 lg:grid-cols-1">
                        @foreach ($vehicle['offers_detail'] as $offer)
                            <li class="flex items-start gap-2 text-sm">
                                <x-ui.icon name="check-circle" :size="17" class="mt-0.5 shrink-0 text-success" />
                                <span>
                                    {{ $offer['title'] }}
                                    @if ($offer['value_amount'] > 0)
                                        <span class="ml-1 text-xs font-bold text-brand-600">worth {{ $inr($offer['value_amount']) }}</span>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    @if ($vehicle['offers_worth'] > 0)
                        <p class="mt-4 flex items-center justify-center gap-2 rounded-lg bg-red-50 px-4 py-3 text-base font-extrabold text-danger">
                            <x-ui.icon name="gift" :size="20" /> Offers worth {{ $inr($vehicle['offers_worth']) }}
                        </p>
                    @endif
                @else
                    <p class="rounded-lg bg-canvas px-3 py-3 text-sm text-muted">No running offers right now — ask us for the best deal.</p>
                @endif

                <button type="button" @click="{{ $openLead('quotation') }}" x-data
                        class="mt-3 flex w-full items-center justify-center gap-1.5 text-xs font-semibold text-brand-500 underline-offset-2 hover:underline">
                    Get a quotation with these offers <x-ui.icon name="arrow-right" :size="14" />
                </button>
            </div>
        </div>

        {{-- Centre column --}}
        <div class="min-w-0 space-y-5 lg:col-span-5" data-reveal-group>

            <div data-reveal>
                <x-ui.finance-options :vehicle="$vehicle" id="price-emi" />
            </div>

            {{-- Running cost --}}
            <div id="running-cost" class="space-y-5" data-reveal>
                <div class="ab-card p-5">
                    <h2 class="flex items-center gap-2 text-base font-bold">
                        <x-ui.tone-icon icon="gauge" tone="info" :size="18" shape="circle" />
                        Running Cost Estimate
                    </h2>
                    @if ($running)
                        <p class="mt-1 text-xs text-muted">Based on {{ $running['daily_km'] }} km per day ({{ $running['fuel'] }}, at today's fuel price)</p>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-lg bg-brand-50 p-3.5">
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-muted"><x-ui.icon name="fuel" :size="14" class="text-brand-500" /> Operating Cost (per km)</p>
                                <p class="mt-1 text-xl font-extrabold text-brand-600">₹{{ number_format($running['per_km'], 2) }}</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-3.5">
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-muted"><x-ui.icon name="clock" :size="14" class="text-info" /> Daily ({{ $running['daily_km'] }} km)</p>
                                <p class="mt-1 text-xl font-extrabold text-info">{{ $inr($running['daily']) }}</p>
                            </div>
                            <div class="rounded-lg bg-accent-50 p-3.5">
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-muted"><x-ui.icon name="calendar" :size="14" class="text-accent-700" /> Monthly Running Cost</p>
                                <p class="mt-1 text-xl font-extrabold text-accent-700">{{ $inr($running['monthly']) }}</p>
                            </div>
                            <div class="rounded-lg bg-red-50 p-3.5">
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-muted"><x-ui.icon name="chart" :size="14" class="text-danger" /> Annual Running Cost</p>
                                <p class="mt-1 text-xl font-extrabold text-danger">{{ $inr($running['annual']) }}</p>
                            </div>
                        </div>
                        <p class="mt-2 text-[10px] text-muted">Month = 30 days, year = 360 days of running. Fuel price from our live rate table.</p>
                    @else
                        <p class="mt-3 rounded-lg bg-canvas px-3 py-3 text-sm text-muted">Running cost will appear once mileage and fuel price are available for this model.</p>
                    @endif
                </div>

            </div>
        </div>

        {{-- Right column --}}
        <div class="min-w-0 space-y-5 lg:col-span-3" data-reveal-group>

            {{-- Availability --}}
            @if ($stock)
                <div class="ab-card p-5" data-reveal>
                    <h2 class="flex items-center gap-2 text-base font-bold">
                        <x-ui.tone-icon icon="box" tone="brand" :size="18" shape="circle" />
                        Availability &amp; Delivery
                    </h2>
                    <p class="mt-3 flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-bold {{ $stock['in_stock'] ? 'bg-brand-50 text-brand-600' : 'bg-red-50 text-danger' }}">
                        <x-ui.icon :name="$stock['in_stock'] ? 'check-circle' : 'warning'" :size="18" />
                        {{ $stock['in_stock'] ? 'In Stock' : 'Out of Stock' }}
                    </p>
                    <dl class="mt-3 space-y-2.5 text-sm">
                        @if ($stock['available_at'])
                            <div class="flex items-start gap-2.5">
                                <x-ui.icon name="pin" :size="16" class="mt-0.5 shrink-0 text-brand-500" />
                                <div><dt class="text-[11px] text-muted">Available at</dt><dd class="font-semibold">{{ $stock['available_at'] }}{{ $stock['location'] ? ', ' . $stock['location'] : '' }}</dd></div>
                            </div>
                        @endif
                        <div class="flex items-start gap-2.5">
                            <x-ui.icon name="truck" :size="16" class="mt-0.5 shrink-0 text-brand-500" />
                            <div><dt class="text-[11px] text-muted">Delivery Time</dt><dd class="font-semibold">{{ $stock['delivery'] }}</dd></div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <x-ui.icon name="box" :size="16" class="mt-0.5 shrink-0 text-brand-500" />
                            <div><dt class="text-[11px] text-muted">Quantity Available</dt><dd class="font-semibold">{{ $stock['qty'] }} {{ $stock['qty'] === 1 ? 'Unit' : 'Units' }}{{ $stock['colour'] ? ' · ' . $stock['colour'] : '' }}</dd></div>
                        </div>
                    </dl>
                    @if ($stock['in_stock'])
                        <p class="mt-3 flex items-center gap-2 rounded-lg border border-brand-100 bg-brand-50/60 px-3 py-2 text-xs font-semibold text-brand-600">
                            <x-ui.icon name="truck" :size="15" /> Book now for quick delivery!
                        </p>
                    @endif
                </div>
            @endif

            {{-- Warranty --}}
            @if ($warranty)
                <div class="ab-card p-5" data-reveal>
                    <h2 class="flex items-center gap-2 text-base font-bold">
                        <x-ui.tone-icon icon="shield" tone="info" :size="18" shape="circle" />
                        Warranty &amp; Service
                    </h2>
                    <dl class="mt-3 divide-y divide-line text-sm">
                        @foreach ([
                            ['shield', 'Vehicle Warranty', $warranty['vehicle']],
                            ['gear', 'Engine Warranty', $warranty['engine']],
                            ['calendar', 'Service Interval', $warranty['service_interval']],
                            ['wrench', 'Free Services', $warranty['free_services']],
                        ] as [$icon, $label, $value])
                            @if ($value)
                                <div class="flex items-start gap-2.5 py-2">
                                    <x-ui.icon :name="$icon" :size="16" class="mt-0.5 shrink-0 text-info" />
                                    <div><dt class="text-[11px] text-muted">{{ $label }}</dt><dd class="font-semibold">{{ $value }}</dd></div>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </div>
            @endif

            {{-- Reviews summary --}}
            <div class="ab-card p-5" data-reveal>
                <div class="flex items-center justify-between gap-3">
                    <h2 class="flex items-center gap-2 text-base font-bold">
                        <x-ui.tone-icon icon="star" tone="accent" :size="18" shape="circle" />
                        Customer Reviews
                    </h2>
                    <a href="#reviews" class="flex items-center gap-0.5 text-xs font-semibold text-brand-500 underline-offset-2 hover:underline">
                        View All <x-ui.icon name="chevron-right" :size="14" />
                    </a>
                </div>

                <div class="mt-3 flex items-center gap-3">
                    <p class="text-3xl font-extrabold leading-none">{{ number_format($vehicle['rating'], 1) }}<span class="text-sm font-semibold text-muted">/5</span></p>
                    <div>
                        <x-ui.rating :rating="$vehicle['rating']" :show-value="false" :size="15" />
                        <p class="text-[11px] text-muted">{{ number_format($vehicle['reviews']) }} reviews</p>
                    </div>
                </div>

                @if (! empty($distribution))
                    <ul class="mt-3 space-y-1">
                        @foreach ($distribution as $stars => $percent)
                            <li class="flex items-center gap-2">
                                <span class="w-6 text-[11px] text-muted">{{ $stars }}★</span>
                                <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-line">
                                    <span class="block h-full rounded-full bg-accent-500" style="width: {{ $percent }}%"></span>
                                </span>
                                <span class="w-8 text-right text-[11px] text-muted">{{ $percent }}%</span>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @forelse (array_slice($reviewList, 0, 2) as $review)
                    <article class="mt-3 border-t border-line pt-3">
                        <div class="flex items-center gap-2">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-brand-50 text-xs font-bold text-brand-600">{{ mb_substr($review['name'], 0, 1) }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold">{{ $review['name'] }}</p>
                                <x-ui.rating :rating="$review['rating']" :show-value="false" :size="11" />
                            </div>
                            <span class="shrink-0 text-[10px] text-muted">{{ $review['date'] }}</span>
                        </div>
                        <p class="mt-1.5 line-clamp-2 text-xs leading-relaxed text-ink-soft">{{ $review['body'] }}</p>
                    </article>
                @empty
                    <p class="mt-3 border-t border-line pt-3 text-xs text-muted">No reviews yet — be the first to <a href="#reviews" class="font-semibold text-brand-500 underline underline-offset-2">write one</a>.</p>
                @endforelse
            </div>

            {{-- Showroom --}}
            @if ($showroom)
                <div class="ab-card overflow-hidden" data-reveal>
                    <div class="p-5 pb-4">
                        <h2 class="flex items-center gap-2 text-base font-bold">
                            <x-ui.tone-icon icon="building" tone="danger" :size="18" shape="circle" />
                            Visit Our Showroom
                        </h2>
                        <div class="mt-3 flex gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold">{{ $showroom['name'] }}</p>
                                @if ($showroom['type'])
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-muted">{{ $showroom['type'] }}</p>
                                @endif
                                @if ($showroom['address'])
                                    <p class="mt-1.5 text-xs leading-relaxed text-ink-soft">{{ $showroom['address'] }}</p>
                                @endif
                            </div>
                            @if ($showroom['image'])
                                <img src="{{ asset($showroom['image']) }}" alt="{{ $showroom['name'] }}"
                                     class="h-20 w-24 shrink-0 rounded-lg border border-line object-cover" loading="lazy">
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-t border-line bg-canvas p-3">
                        <a href="{{ $showroom['maps_url'] }}" target="_blank" rel="noopener" class="ab-btn ab-btn-primary px-2 text-xs">
                            <x-ui.icon name="navigation" :size="15" /> Get Directions
                        </a>
                        @if ($showroom['contact'])
                            <a href="tel:{{ preg_replace('/\D+/', '', (string) $showroom['contact']) }}" class="ab-btn ab-btn-outline px-2 text-xs">
                                <x-ui.icon name="phone-call" :size="15" /> Call
                            </a>
                        @else
                            <a href="tel:{{ $site['contact']['phone_e164'] }}" class="ab-btn ab-btn-outline px-2 text-xs">
                                <x-ui.icon name="phone-call" :size="15" /> Call
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Documents --}}
            @if (! empty($documents))
                <div class="ab-card p-5" data-reveal>
                    <h2 class="flex items-center gap-2 text-base font-bold">
                        <x-ui.tone-icon icon="doc" tone="muted" :size="18" shape="circle" />
                        Documents &amp; Downloads
                    </h2>
                    <ul class="mt-3 divide-y divide-line">
                        @foreach ($documents as $doc)
                            <li>
                                <a href="{{ $doc['url'] }}" target="_blank" rel="noopener"
                                   class="flex items-center gap-2.5 py-2 text-sm font-semibold transition-colors hover:text-brand-500">
                                    <x-ui.icon name="download" :size="16" class="shrink-0 text-brand-500" />
                                    <span class="min-w-0 flex-1 truncate">{{ $doc['title'] }}</span>
                                    @if ($doc['type'])
                                        <span class="shrink-0 rounded bg-canvas px-1.5 py-0.5 text-[10px] font-bold uppercase text-muted">{{ $doc['type'] }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>

    {{-- ===================================================== cost calculators
         Side by side under the three columns, so the centre column stays the
         same height as its neighbours instead of leaving blank space. --}}
    <section class="mt-5 grid items-start gap-5 lg:grid-cols-2" aria-label="Cost calculators" data-reveal-group>
        <div class="min-w-0" data-reveal>
            <x-ui.earnings-calculator :vehicle="$vehicle" :open="true" />
        </div>
        @if (! empty($vehicle['operating_cost_fuels']))
            <div class="min-w-0" data-reveal>
                <x-ui.operating-cost-panel :vehicle="$vehicle" />
            </div>
        @endif
    </section>

    {{-- ====================================================== specifications --}}
    <section id="specifications" class="mt-10" aria-labelledby="specs-title">
        <x-ui.section-heading title="Full Specifications" level="h2"
                              lede="Every figure from the manufacturer's spec sheet, plus our score breakdown." />
        <div class="grid gap-5 lg:grid-cols-3">
            <div class="min-w-0 lg:col-span-2" data-reveal>
                <x-ui.spec-table :specifications="$vehicle['specifications']" :full="true" />
            </div>
            <div data-reveal>
                <x-ui.score-panel :scores="$scores" />
            </div>
        </div>
        <div class="mt-5" data-reveal>
            <x-ui.suitability :vehicle="$vehicle" />
        </div>
    </section>

    {{-- ============================================================= reviews --}}
    <section id="reviews" class="mt-10" aria-labelledby="reviews-title">
        <x-ui.section-heading title="Customer Reviews" level="h2"
                              :lede="'What ' . $vehicle['name'] . ' owners say — ' . number_format($vehicle['reviews']) . ' verified and community reviews.'" />

        <div class="grid gap-5 lg:grid-cols-3">
            <div class="space-y-5" data-reveal>
                <div class="ab-card p-5">
                    <p class="text-4xl font-extrabold">{{ number_format($vehicle['rating'], 1) }}</p>
                    <x-ui.rating :rating="$vehicle['rating']" :show-value="false" :size="16" class="mt-1" />
                    <p class="mt-1 text-xs text-muted">Based on {{ number_format($vehicle['reviews']) }} reviews</p>
                    @if (! empty($distribution))
                        <ul class="mt-4 space-y-1.5">
                            @foreach ($distribution as $stars => $percent)
                                <li class="flex items-center gap-2">
                                    <span class="w-8 text-[11px] text-muted">{{ $stars }} ★</span>
                                    <span class="h-1.5 flex-1 overflow-hidden rounded-full bg-line">
                                        <span class="block h-full rounded-full bg-accent-500" style="width: {{ $percent }}%"></span>
                                    </span>
                                    <span class="w-8 text-right text-[11px] text-muted">{{ $percent }}%</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <x-ui.review-form :vehicle="$vehicle" :url="$reviewUrl" />
            </div>

            <ul class="min-w-0 space-y-3 lg:col-span-2" data-reveal-group>
                @forelse ($reviewList as $review)
                    <li class="ab-card p-4" data-reveal>
                        <article>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="grid h-8 w-8 place-items-center rounded-full bg-brand-50 text-xs font-bold text-brand-600">{{ mb_substr($review['name'], 0, 1) }}</span>
                                <span class="text-sm font-bold">{{ $review['name'] }}</span>
                                @if ($review['city'])
                                    <span class="text-xs text-muted">· {{ $review['city'] }}</span>
                                @endif
                                @if ($review['verified'])
                                    <span class="flex items-center gap-1 rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-600">
                                        <x-ui.icon name="check" :size="11" /> Verified Buyer
                                    </span>
                                @endif
                                <span class="ml-auto text-[11px] text-muted">{{ $review['date'] }}</span>
                            </div>
                            <x-ui.rating :rating="$review['rating']" :show-value="false" :size="13" class="mt-2.5" />
                            @if ($review['title'])
                                <h3 class="mt-1.5 text-sm font-bold">{{ $review['title'] }}</h3>
                            @endif
                            <p class="mt-1 text-sm leading-relaxed text-ink-soft">{{ $review['body'] }}</p>
                        </article>
                    </li>
                @empty
                    <li class="ab-card p-8 text-center text-sm text-muted" data-reveal>
                        No reviews yet. Own this auto? Share your experience with the form.
                    </li>
                @endforelse
            </ul>
        </div>
    </section>

    @if (count($images) > 1)
    {{-- ============================================================= gallery --}}
    <section id="gallery" class="mt-10" aria-labelledby="gallery-title">
        <x-ui.section-heading title="Gallery" level="h2" :lede="count($images) . ' ' . (count($images) === 1 ? 'photo' : 'photos') . ' of the ' . $vehicle['name']" />
        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4" data-reveal-group>
            @foreach ($images as $i => $img)
                <li class="ab-card ab-lift overflow-hidden bg-canvas" data-reveal>
                    <img src="{{ $img }}" alt="{{ $vehicle['name'] }} photo {{ $i + 1 }}"
                         class="aspect-4/3 w-full object-contain p-3" loading="lazy">
                </li>
            @endforeach
        </ul>
    </section>
    @endif

    {{-- ============================================================= similar --}}
    @if (! empty($similar))
        <section class="mt-10" aria-label="Similar models">
            <x-ui.section-heading title="Similar Models" :href="route('site.compare')" link-label="Compare All" />
            <ul class="grid grid-cols-2 gap-4 lg:grid-cols-4" data-reveal-group>
                @foreach ($similar as $other)
                    <li data-reveal><x-ui.vehicle-card :vehicle="$other" class="h-full" /></li>
                @endforeach
            </ul>
        </section>
    @endif
</div>

<x-ui.lead-modal :vehicle="$vehicle" :site="$site" :url="$leadUrl" />
<x-ui.sticky-actions :vehicle="$vehicle" :site="$site" />

@endsection
