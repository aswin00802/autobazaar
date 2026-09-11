@extends('site.layout')

@section('title', $vehicle['name'])
@section('description', $vehicle['description'])

@section('content')

@php
    $default = $locations['default'];
    $onRoad = $vehicle['on_road_price'];
    $tabs = [
        'overview' => 'Overview',
        'specifications' => 'Specifications',
        'scores' => 'Scores & Rating',
        'buying' => 'Buying Options',
        'emi' => 'EMI Calculator',
        'operating' => 'Operating Cost',
        'suitability' => 'Suitability',
        'images' => 'Images',
        'reviews' => 'Reviews',
    ];
@endphp

<div class="ab-container py-4">
    <x-ui.breadcrumb :items="[
        ['label' => 'Home', 'href' => route('site.home')],
        ['label' => 'Vehicles', 'href' => route('site.new-autos')],
        ['label' => $vehicle['brand'], 'href' => route('site.brand', $vehicle['brand_slug'])],
        ['label' => $vehicle['name']],
    ]" />
</div>

{{-- ============================================================ hero section --}}
<section class="ab-container pb-8">
    <div class="grid gap-6 lg:grid-cols-12">

        {{-- Gallery --}}
        <div class="lg:col-span-4"
             x-data="{ active: 0, images: {{ Js::from(array_fill(0, 5, $vehicle['image'])) }} }">
            <div class="ab-card relative overflow-hidden">
                @if ($vehicle['badge'])
                    <span class="absolute left-3 top-3 z-10 rounded bg-danger px-2.5 py-1 text-[11px] font-bold text-white">
                        {{ $vehicle['badge'] }}
                    </span>
                @endif

                <img src="{{ asset($vehicle['brand_logo']) }}" alt="{{ $vehicle['brand'] }}"
                     class="absolute right-4 top-4 z-10 h-6 w-auto object-contain">

                <img :src="images[active]" src="{{ asset($vehicle['image']) }}"
                     alt="{{ $vehicle['name'] }}"
                     class="aspect-4/3 w-full bg-canvas object-contain p-6">

                {{-- Prev / next --}}
                <button type="button" @click="active = (active - 1 + images.length) % images.length"
                        class="absolute left-2 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-full
                               bg-surface/90 shadow transition-colors hover:bg-surface"
                        aria-label="Previous image">
                    <x-ui.icon name="chevron-left" :size="18" />
                </button>
                <button type="button" @click="active = (active + 1) % images.length"
                        class="absolute right-2 top-1/2 grid h-9 w-9 -translate-y-1/2 place-items-center rounded-full
                               bg-surface/90 shadow transition-colors hover:bg-surface"
                        aria-label="Next image">
                    <x-ui.icon name="chevron-right" :size="18" />
                </button>
            </div>

            {{-- Thumbnails --}}
            <ul class="mt-3 grid grid-cols-5 gap-2">
                <template x-for="(img, i) in images" :key="i">
                    <li>
                        <button type="button" @click="active = i"
                                :class="active === i ? 'border-brand-500' : 'border-line hover:border-brand-300'"
                                class="w-full overflow-hidden rounded-lg border-2 bg-canvas transition-colors"
                                :aria-label="`View image ${i + 1}`">
                            <img :src="img" alt="" class="aspect-4/3 w-full object-contain p-1">
                        </button>
                    </li>
                </template>
            </ul>
        </div>

        {{-- Summary --}}
        <div class="lg:col-span-5" data-reveal="right">
            <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $vehicle['name'] }}</h1>
            <p class="mt-1 text-sm text-muted">{{ $vehicle['tagline'] }}</p>

            {{-- Fuel variants --}}
            <div class="mt-4 flex flex-wrap gap-2" x-data="{ variant: @js($vehicle['variants'][0]['key']) }">
                @foreach ($vehicle['variants'] as $v)
                    <button type="button" @click="variant = @js($v['key'])"
                            :class="variant === @js($v['key'])
                                ? 'border-brand-500 bg-brand-50 text-brand-600'
                                : 'border-line bg-surface text-ink-soft hover:border-brand-300'"
                            class="flex items-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-semibold transition-colors">
                        <x-ui.icon :name="$v['icon']" :size="15" />
                        {{ $v['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Rating --}}
            <div class="mt-4 flex flex-wrap items-center gap-3">
                <x-ui.rating :rating="$vehicle['rating']" :reviews="$vehicle['reviews']" :size="16" />
                <a href="#reviews" class="text-xs font-semibold text-brand-500 underline underline-offset-2">
                    Write a Review
                </a>
            </div>

            <p class="mt-4 text-sm leading-relaxed text-ink-soft">{{ $vehicle['description'] }}</p>

            {{-- Feature strip --}}
            <ul class="mt-5 flex flex-wrap gap-x-5 gap-y-2 rounded-lg bg-canvas px-4 py-3">
                @foreach ($vehicle['features'] as $feature)
                    <li class="flex items-center gap-1.5 text-xs font-semibold">
                        <x-ui.icon :name="$feature['icon']" :size="16" class="text-brand-500" />
                        {{ $feature['label'] }}
                    </li>
                @endforeach
            </ul>

            {{-- Primary actions --}}
            <div class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-4">
                <a href="{{ route('site.enquiry', $vehicle['slug']) }}" class="ab-btn ab-btn-primary text-xs">
                    <x-ui.icon name="whatsapp" :size="16" /> Enquire Now
                </a>

                <a href="{{ route('site.buying-options') }}" class="ab-btn ab-btn-accent text-xs">
                    <x-ui.icon name="doc" :size="16" /> Book Now
                </a>

                <div x-data>
                    <button type="button"
                            @click="$store.compare.toggle({ slug: @js($vehicle['slug']), name: @js($vehicle['name']) })"
                            :aria-pressed="$store.compare.has(@js($vehicle['slug']))"
                            class="ab-btn ab-btn-ghost w-full text-xs">
                        <x-ui.icon name="scales" :size="16" />
                        <span x-text="$store.compare.has(@js($vehicle['slug'])) ? 'Added' : 'Compare'">Compare</span>
                    </button>
                </div>

                <button type="button" class="ab-btn ab-btn-ghost text-xs">
                    <x-ui.icon name="share" :size="16" /> Share
                </button>
            </div>
        </div>

        {{-- Price + buying panel --}}
        <div class="space-y-3 lg:col-span-3">

            {{-- Location price tabs --}}
            <div class="ab-card overflow-hidden" x-data="{ tab: 'local' }">
                <div class="grid grid-cols-3 border-b border-line text-[11px] font-semibold">
                    @foreach ([
                        ['local', 'pin', 'Price in Your Location'],
                        ['tn', 'map', 'Other Tamil Nadu Districts'],
                        ['other', 'flag', 'Other States'],
                    ] as [$key, $icon, $label])
                        <button type="button" @click="tab = @js($key)"
                                :class="tab === @js($key) ? 'bg-accent-500 text-ink' : 'bg-surface text-muted hover:bg-canvas'"
                                class="flex flex-col items-center gap-1 px-2 py-2.5 transition-colors">
                            <x-ui.icon :name="$icon" :size="14" />
                            <span class="text-center leading-tight">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Direct-purchase district --}}
                <div x-show="tab === 'local'" class="p-4">
                    <p class="mb-3 flex items-center gap-2 rounded-lg bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-600">
                        <x-ui.icon name="check-circle" :size="16" />
                        Direct purchase available in {{ $default['city'] }}
                    </p>

                    <dl class="space-y-2 text-xs">
                        @foreach ($vehicle['price_breakup'] as $row)
                            <div class="flex items-center justify-between gap-3">
                                <dt class="text-muted">{{ $row['label'] }}</dt>
                                <dd class="font-semibold">₹{{ number_format($row['amount']) }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-4 rounded-lg bg-accent-50 px-3 py-3">
                        <p class="text-[11px] font-semibold text-ink-soft">On-Road Price ({{ $default['city'] }})</p>
                        <p class="text-2xl font-extrabold text-brand-500">₹{{ number_format($onRoad) }}*</p>
                        <p class="mt-1 text-[10px] text-muted">
                            *Price may vary based on offers and insurance options.
                        </p>
                    </div>
                </div>

                {{-- Other TN districts --}}
                <div x-show="tab === 'tn'" x-cloak class="p-4">
                    <p class="mb-3 flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-xs font-semibold text-info">
                        <x-ui.icon name="handshake" :size="16" /> Buying assistance available
                    </p>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between"><dt class="text-muted">Ex-Showroom Price</dt>
                            <dd class="font-semibold">₹{{ number_format($vehicle['ex_showroom']) }}</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">RTO & Insurance</dt>
                            <dd class="font-semibold">Varies by district</dd></div>
                    </dl>
                    <a href="{{ route('site.buying-options') }}" class="ab-btn ab-btn-primary mt-4 w-full text-xs">
                        Get Buying Assistance
                    </a>
                </div>

                {{-- Other states --}}
                <div x-show="tab === 'other'" x-cloak class="p-4">
                    <p class="mb-3 flex items-center gap-2 rounded-lg bg-orange-50 px-3 py-2 text-xs font-semibold text-warn">
                        <x-ui.icon name="map" :size="16" /> We will connect you with a nearby dealer
                    </p>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between"><dt class="text-muted">Ex-Showroom Price</dt>
                            <dd class="font-semibold">Where available</dd></div>
                        <div class="flex justify-between"><dt class="text-muted">Specifications & Features</dt>
                            <dd class="font-semibold">Available</dd></div>
                    </dl>
                    <a href="{{ route('site.buying-options') }}" class="ab-btn ab-btn-primary mt-4 w-full text-xs">
                        Find a Dealer Near You
                    </a>
                </div>
            </div>

            {{-- Location note --}}
            <div class="ab-card p-4">
                <p class="text-[11px] text-muted">You are viewing price for</p>
                <p class="flex items-center gap-1.5 text-sm font-bold">
                    <x-ui.icon name="pin" :size="15" class="text-brand-500" />
                    {{ $default['city'] }}, {{ $default['state'] }}
                </p>
                <a href="{{ route('site.buying-options') }}"
                   class="mt-1 inline-block text-xs font-semibold text-brand-500 underline underline-offset-2">
                    Change Location
                </a>
            </div>

            {{-- Buying options --}}
            <div class="ab-card bg-accent-50 p-4">
                <p class="mb-2.5 flex items-center gap-2 text-sm font-bold">
                    <x-ui.icon name="gear" :size="16" /> Buying Option Available
                </p>
                <ul class="space-y-1.5">
                    @foreach (['Net Cash Purchase', 'Private Finance', 'Bank Loan', 'EMI Calculator', 'Exchange Offer'] as $option)
                        <li class="flex items-center gap-2 text-xs">
                            <x-ui.icon name="check" :size="13" class="text-success" /> {{ $option }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Offers --}}
            @if (! empty($vehicle['offers']))
                <div class="ab-card bg-accent-50 p-4">
                    <p class="mb-2.5 flex items-center gap-2 text-sm font-bold">
                        <x-ui.icon name="tag" :size="16" /> Available Offers
                    </p>
                    <ul class="space-y-1.5">
                        @foreach ($vehicle['offers'] as $offer)
                            <li class="flex items-start gap-2 text-xs">
                                <x-ui.icon name="check" :size="13" class="mt-0.5 text-success" /> {{ $offer }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</section>

{{-- ================================================================ tab area --}}
<section class="ab-container pb-10" x-data="{ tab: 'overview' }">

    {{-- Tab bar --}}
    <div class="ab-card mb-5 overflow-hidden">
        <div class="ab-scroll-x gap-0 border-b border-line pb-0" role="tablist">
            @foreach ($tabs as $key => $label)
                <button type="button" role="tab"
                        @click="tab = @js($key)"
                        :aria-selected="tab === @js($key)"
                        :class="tab === @js($key)
                            ? 'border-brand-500 text-brand-600'
                            : 'border-transparent text-muted hover:text-ink'"
                        class="whitespace-nowrap border-b-2 px-4 py-3 text-sm font-semibold transition-colors">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ------------------------------------------------------------ overview --}}
    <div x-show="tab === 'overview'" role="tabpanel">
        <div class="grid gap-5 lg:grid-cols-2">
            <x-ui.spec-table :specifications="$vehicle['specifications']"
                             :href="route('site.model', [$vehicle['brand_slug'], $vehicle['model_slug']])" />
            <x-ui.score-panel :scores="$vehicle['scores']" />
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-2">
            <x-ui.emi-panel :vehicle="$vehicle" />
            <x-ui.operating-cost-panel :vehicle="$vehicle" />
        </div>

        <div class="mt-5">
            <x-ui.suitability :vehicle="$vehicle" />
        </div>
    </div>

    {{-- ------------------------------------------------------ specifications --}}
    <div x-show="tab === 'specifications'" x-cloak role="tabpanel">
        <x-ui.spec-table :specifications="$vehicle['specifications']" :full="true" />
    </div>

    {{-- -------------------------------------------------------------- scores --}}
    <div x-show="tab === 'scores'" x-cloak role="tabpanel" class="max-w-3xl">
        <x-ui.score-panel :scores="$vehicle['scores']" />
    </div>

    {{-- ------------------------------------------------------ buying options --}}
    <div x-show="tab === 'buying'" x-cloak role="tabpanel">
        <div class="grid gap-4 md:grid-cols-3">
            @foreach ($locations['tiers'] as $tier)
                <x-ui.tier-card :tier="$tier" />
            @endforeach
        </div>
    </div>

    {{-- ----------------------------------------------------- EMI calculator --}}
    <div x-show="tab === 'emi'" x-cloak role="tabpanel" class="max-w-2xl">
        <x-ui.emi-panel :vehicle="$vehicle" />
    </div>

    {{-- ---------------------------------------------------- operating cost --}}
    <div x-show="tab === 'operating'" x-cloak role="tabpanel" class="max-w-2xl">
        <x-ui.operating-cost-panel :vehicle="$vehicle" />
    </div>

    {{-- --------------------------------------------------------- suitability --}}
    <div x-show="tab === 'suitability'" x-cloak role="tabpanel">
        <x-ui.suitability :vehicle="$vehicle" />
    </div>

    {{-- -------------------------------------------------------------- images --}}
    <div x-show="tab === 'images'" x-cloak role="tabpanel">
        <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @for ($i = 0; $i < 8; $i++)
                <li class="ab-card overflow-hidden bg-canvas">
                    <img src="{{ asset($vehicle['image']) }}" alt="{{ $vehicle['name'] }} photo {{ $i + 1 }}"
                         class="aspect-4/3 w-full object-contain p-3" loading="lazy">
                </li>
            @endfor
        </ul>
    </div>

    {{-- ------------------------------------------------------------- reviews --}}
    <div x-show="tab === 'reviews'" x-cloak role="tabpanel" id="reviews">
        <x-ui.reviews :vehicle="$vehicle" />
    </div>
</section>

{{-- ========================================================= service network --}}
<section class="ab-container pb-10">
    <div class="ab-card flex flex-wrap items-center justify-between gap-4 bg-brand-50 p-5">
        <div class="flex items-center gap-3">
            <x-ui.icon name="wrench" :size="26" class="text-brand-500" />
            <div>
                <p class="text-sm font-bold">Service Network</p>
                <p class="text-xs text-muted">Wide service network across Tamil Nadu</p>
            </div>
        </div>
        <a href="{{ route('site.contact') }}"
           class="flex items-center gap-1.5 text-sm font-semibold text-brand-500 underline underline-offset-2">
            Find Service Center <x-ui.icon name="arrow-right" :size="15" />
        </a>
    </div>
</section>

{{-- ================================================================= similar --}}
<section class="ab-container pb-10">
    <x-ui.section-heading title="Similar Models" :href="route('site.compare')" link-label="Compare All" />

    <ul class="grid grid-cols-2 gap-4 lg:grid-cols-4" data-reveal-group>
        @foreach ($similar as $other)
            <li data-reveal><x-ui.vehicle-card :vehicle="$other" class="h-full" /></li>
        @endforeach
    </ul>
</section>

@endsection
