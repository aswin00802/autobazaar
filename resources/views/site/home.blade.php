@extends('site.layout')

@section('title', 'India\'s Trusted Auto Marketplace')

@section('content')

{{-- ===================================================================== hero --}}
<section class="relative overflow-hidden bg-brand-700 text-white">
    {{-- Client-supplied banner (home_banner.png, served as WebP).
         Its left third is bright sky, so the headline and search card need a
         real scrim; the right third carries the baked-in "More Miles More
         Possibilities" lettering, which is why nothing is placed over it. --}}
    <div class="absolute inset-0" aria-hidden="true">
        <img src="{{ asset('assets/site/home-banner.webp') }}" alt=""
             class="h-full w-full object-cover object-center">

        <div class="absolute inset-0 bg-gradient-to-r from-ink/85 via-ink/55 to-transparent"></div>
        <div class="absolute inset-0 bg-ink/40 lg:hidden"></div>
    </div>

    <div class="ab-container relative py-10 lg:py-12">
        <div class="grid items-center gap-10 lg:grid-cols-12">

            {{-- Copy + search --}}
            <div class="lg:col-span-7" data-reveal="left">
                <p class="mb-4 inline-block rounded border border-white/30 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.16em]">
                    India's Trusted Auto Marketplace
                </p>

                <h1 class="text-3xl font-extrabold leading-[1.15] tracking-tight sm:text-4xl lg:text-5xl">
                    Find the Right Autorickshaw<br class="hidden sm:block">
                    for a <span class="text-accent-500">Better Tomorrow</span>
                </h1>

                <p class="mt-3 text-sm text-white/85 sm:text-base">
                    Compare | Check Prices | Calculate EMI | Explore Offers
                </p>

                {{-- Four-field search --}}
                <form action="{{ route('site.search') }}" method="GET"
                      class="mt-7 rounded-xl bg-surface p-4 text-ink shadow-lg">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <label for="hero-brand" class="ab-label">Brand</label>
                            <select id="hero-brand" name="brand" class="ab-field">
                                <option value="">Select Brand</option>
                                @foreach (collect($vehicles)->pluck('brand')->unique() as $brand)
                                    <option>{{ $brand }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="hero-model" class="ab-label">Model</label>
                            <select id="hero-model" name="model" class="ab-field">
                                <option value="">Select Model</option>
                                @foreach ($vehicles as $v)
                                    <option>{{ $v['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="hero-fuel" class="ab-label">Fuel Type</label>
                            <select id="hero-fuel" name="fuel" class="ab-field">
                                <option value="">Select Fuel Type</option>
                                <option>Petrol</option><option>CNG</option>
                                <option>Diesel</option><option>Electric</option><option>LPG</option>
                            </select>
                        </div>

                        <div>
                            <label for="hero-price" class="ab-label">Price Range</label>
                            <select id="hero-price" name="price" class="ab-field">
                                <option value="">Select Range</option>
                                <option>Under ₹2.5 Lakh</option>
                                <option>₹2.5 – ₹3 Lakh</option>
                                <option>₹3 – ₹4 Lakh</option>
                                <option>Above ₹4 Lakh</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit" class="ab-btn ab-btn-primary w-full py-2.5">
                                <x-ui.icon name="search" :size="18" />
                                Search Autos
                            </button>
                        </div>
                    </div>
                </form>

                {{-- USP row --}}
                <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-3">
                    @foreach ([['fuel', 'Best Mileage'], ['wrench', 'Low Maintenance'], ['rupee', 'Easy Finance'], ['shield', 'Trusted Brands']] as [$icon, $label])
                        <li class="flex items-center gap-2 text-sm font-semibold text-white/90">
                            <x-ui.icon :name="$icon" :size="18" class="text-accent-500" />
                            {{ $label }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Featured offer, top-right over the hero as the reference has it.
                 NOTE: the client's banner has its script lettering running from
                 55% to 100% of the width, where the reference's stops near 79%,
                 so this card sits over the tail of it. Fix is a banner whose
                 lettering ends by ~75%, not a layout change here. --}}
            <div class="lg:col-span-5" data-reveal="right">
                @php $featured = collect($offers)->firstWhere('featured', true); @endphp

                @if ($featured)
                    <div class="ab-card mx-auto max-w-sm p-4 text-ink shadow-xl lg:ml-auto lg:mr-0 lg:w-72">
                        <img src="{{ asset($featured['brand_logo']) }}" alt="{{ $featured['brand'] }}"
                             class="mb-2 h-6 w-auto object-contain object-left">

                        <p class="text-base font-extrabold leading-tight">{{ $featured['title'] }}</p>
                        <p class="mt-1.5 text-xs text-muted">{{ $featured['benefit_label'] }}</p>
                        <p class="text-3xl font-extrabold text-brand-500">{{ $featured['benefit'] }}</p>

                        <ul class="mt-3 space-y-1.5">
                            @foreach ($featured['bullets'] as $bullet)
                                <li class="flex items-start gap-2 text-xs">
                                    <x-ui.icon name="check" :size="14" class="mt-0.5 shrink-0 text-success" />
                                    {{ $bullet }}
                                </li>
                            @endforeach
                        </ul>

                        <a href="{{ route('site.offers') }}"
                           class="ab-btn ab-btn-accent mt-4 w-full py-2 text-xs">
                            {{ $featured['cta'] }} <x-ui.icon name="arrow-right" :size="14" />
                        </a>

                        <p class="mt-1.5 text-right text-[10px] text-muted">{{ $featured['note'] }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ quick actions --}}
<section class="ab-container -mt-px py-8">
    <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-9" data-reveal-group>
        @foreach ($site['quick_actions'] as $action)
            <li data-reveal>
                <a href="{{ route($action['route']) }}"
                   class="ab-card flex h-full flex-col items-center gap-1.5 px-2 py-4 text-center
                          ab-lift">
                    <x-ui.tone-icon :icon="$action['icon']" :tone="$action['tone']" />
                    <span class="mt-1 text-xs font-bold leading-tight">{{ $action['label'] }}</span>
                    <span class="text-[10px] leading-tight text-muted">{{ $action['note'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</section>

{{-- ================================================= popular models + offers --}}
<section class="ab-container pb-10">
    <div class="grid gap-8 lg:grid-cols-12">

        {{-- Popular models --}}
        <div class="lg:col-span-7">
            <x-ui.section-heading title="Popular Autorickshaw Models"
                                  :href="route('site.new-autos')" />

            <ul class="ab-scroll-x snap-x" data-reveal-group>
                @foreach ($vehicles as $vehicle)
                    <li class="w-52 shrink-0 snap-start" data-reveal>
                        <x-ui.vehicle-card :vehicle="$vehicle" class="h-full" />
                    </li>
                @endforeach
            </ul>

            {{-- Three CTA cards --}}
            <div class="mt-6 grid gap-3 sm:grid-cols-3" data-reveal-group>
                @foreach ([
                    ['calculator', 'Calculate Your EMI', 'Plan your autorickshaw easily', 'site.finance', 'accent'],
                    ['rupee', 'Check Operating Cost', 'Know your daily earnings', 'site.finance', 'brand'],
                    ['headset', 'Get Buying Assistance', "Not in our direct sales area? We'll guide you.", 'site.buying-options', 'muted'],
                ] as [$icon, $title, $note, $route, $tone])
                    <a href="{{ route($route) }}"
                       class="flex items-center gap-3 rounded-xl px-4 py-3.5 transition-opacity hover:opacity-90
                              {{ $tone === 'accent' ? 'bg-accent-500 text-ink'
                                 : ($tone === 'brand' ? 'bg-brand-500 text-white' : 'bg-brand-50 text-ink') }}">
                        <x-ui.icon :name="$icon" :size="22" />
                        <span class="flex-1 leading-tight">
                            <span class="block text-sm font-bold">{{ $title }}</span>
                            <span class="block text-[11px] opacity-80">{{ $note }}</span>
                        </span>
                        <x-ui.icon name="arrow-right" :size="16" />
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Latest offers + popular accessories --}}
        <div class="lg:col-span-5">
            <x-ui.section-heading title="Latest Offers" :href="route('site.offers')" />

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1" data-reveal-group>
                @foreach (collect($offers)->where('featured', false) as $offer)
                    <article class="ab-card flex items-center gap-4 overflow-hidden p-4
                                    {{ $offer['tone'] === 'info' ? 'bg-blue-50' : 'bg-orange-50' }}">
                        <div class="flex-1">
                            <img src="{{ asset($offer['brand_logo']) }}" alt="{{ $offer['brand'] }}"
                                 class="mb-1.5 h-4 w-auto object-contain object-left">

                            <p class="text-sm font-bold leading-tight">{{ $offer['title'] }}</p>
                            @if ($offer['subtitle'])
                                <p class="text-sm font-bold leading-tight">{{ $offer['subtitle'] }}</p>
                            @endif

                            @if ($offer['benefit'])
                                <p class="mt-0.5 text-[11px] text-muted">{{ $offer['benefit_label'] }}</p>
                                <p class="text-lg font-extrabold text-danger">{{ $offer['benefit'] }}</p>
                            @elseif (! empty($offer['bullets']))
                                <p class="mt-0.5 text-[11px] text-muted">{{ $offer['bullets'][0] }}</p>
                            @endif

                            <a href="{{ route('site.offers') }}"
                               class="ab-btn ab-btn-accent mt-2 px-3 py-1.5 text-[11px]">{{ $offer['cta'] }}</a>
                        </div>

                        <img src="{{ asset($offer['image']) }}" alt="" aria-hidden="true"
                             class="h-24 w-28 shrink-0 object-contain" loading="lazy">
                    </article>
                @endforeach
            </div>

            {{-- Reference puts these in the right rail under Latest Offers,
                 as a single row of six compact tiles. --}}
            <x-ui.section-heading title="Popular Accessories" class="mt-8"
                                  :href="route('site.accessories.shop')" />

            <ul class="grid grid-cols-3 gap-3 sm:grid-cols-6" data-reveal-group>
                @foreach ($accessories as $product)
                    <li data-reveal>
                        <a href="{{ route('site.accessories.shop') }}"
                           class="flex h-full flex-col items-center gap-1 text-center">
                            <span class="grid h-16 w-full place-items-center rounded-lg bg-canvas">
                                @if (! empty($product['image']))
                                    <img src="{{ asset($product['image']) }}" alt="" aria-hidden="true"
                                         class="h-12 w-auto object-contain" loading="lazy">
                                @else
                                    <x-ui.product-art :art="$product['art']" class="h-10 w-auto" />
                                @endif
                            </span>
                            <span class="line-clamp-2 text-[10px] font-semibold leading-tight">
                                {{ $product['name'] }}
                            </span>
                            <span class="text-[11px] font-extrabold">₹{{ number_format($product['price']) }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>


{{-- ================================================================ trust bar --}}
<section class="bg-brand-700 text-white">
    <div class="ab-container flex flex-wrap items-center gap-x-10 gap-y-5 py-5">

        <ul class="flex flex-wrap items-center gap-x-9 gap-y-5" data-reveal-group>
            @foreach ($site['stats'] as $stat)
                <li class="flex items-center gap-2.5" data-reveal>
                    <x-ui.icon :name="$stat['icon']" :size="26" class="shrink-0 text-accent-500" />
                    <span class="leading-tight">
                        <span class="block text-base font-extrabold">{{ $stat['value'] }}</span>
                        <span class="block whitespace-nowrap text-[11px] text-white/75">{{ $stat['label'] }}</span>
                    </span>
                </li>
            @endforeach
        </ul>

        <p class="flex flex-1 items-start gap-3 border-white/15 text-xs leading-relaxed text-white/85
                  xl:min-w-96 xl:border-l xl:pl-8">
            <x-ui.icon name="pin" :size="20" class="mt-0.5 shrink-0 text-accent-500" />
            <span>
                {{ $site['service_area']['headline'] }}
                {{ $site['service_area']['note'] }}
                <a href="{{ route('site.buying-options') }}"
                   class="font-semibold text-accent-500 underline-offset-2 hover:underline">Know More →</a>
            </span>
        </p>
    </div>
</section>

@endsection
