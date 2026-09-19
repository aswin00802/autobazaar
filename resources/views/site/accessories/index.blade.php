@extends('site.layout')

@section('title', 'Auto Accessories')
@section('description', 'Premium accessories for your auto rickshaw — floor mats, seat covers, LED lights, mirrors and more.')

@section('content')

<div class="ab-container py-4">
    <x-ui.breadcrumb :items="[
        ['label' => 'Home', 'href' => route('site.home')],
        ['label' => 'Accessories'],
    ]" />
</div>

{{-- ==================================================================== hero --}}
<section class="relative overflow-hidden bg-ink text-white">
    <div class="absolute inset-0 bg-gradient-to-r from-ink via-ink/95 to-ink/60" aria-hidden="true"></div>

    <div class="ab-container relative py-10">
        <div class="grid gap-8 lg:grid-cols-12 lg:items-center">

            <div class="min-w-0 lg:col-span-6">
                <h1 class="text-3xl font-extrabold tracking-tight sm:text-4xl">
                    Auto Accessories
                </h1>
                <p class="mt-1 text-xl font-extrabold text-accent-500">Personalize. Protect. Perform.</p>
                <p class="mt-2 text-sm text-white/80">Premium accessories for your Auto Rickshaw.</p>

                <ul class="mt-6 flex flex-wrap gap-x-6 gap-y-3">
                    @foreach ([
                        ['shield', 'Quality Products', 'Trusted Brands'],
                        ['rupee', 'Best Prices', 'Great Deals'],
                        ['wrench', 'Professional Fitment', 'Available in Chennai'],
                    ] as [$icon, $title, $note])
                        <li class="flex items-center gap-2.5">
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-brand-500">
                                <x-ui.icon :name="$icon" :size="18" />
                            </span>
                            <span class="leading-tight">
                                <span class="block text-sm font-bold">{{ $title }}</span>
                                <span class="block text-[11px] text-white/70">{{ $note }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="relative lg:col-span-3">
                <img src="{{ asset('assets/image/auto_brands/tvs.png') }}" alt="" aria-hidden="true"
                     class="mx-auto w-full max-w-xs" loading="lazy">
                <p class="ab-script mt-2 text-center text-xl leading-tight text-accent-500">
                    Upgrade Your Ride Everyday!
                </p>
            </div>

            {{-- Delivery / help panel --}}
            <div class="space-y-2.5 lg:col-span-3">
                @foreach ([
                    ['truck', 'Free Installation', 'in ' . implode(', ', $site['service_area']['districts'])],
                    ['box', 'All India', 'Shipping Available'],
                    ['headset', 'Need Help?', $site['contact']['phone']],
                ] as [$icon, $title, $note])
                    <div class="flex items-center gap-3 rounded-lg bg-accent-50 px-4 py-3 text-ink">
                        <x-ui.icon :name="$icon" :size="20" class="text-brand-500" />
                        <span class="leading-tight">
                            <span class="block text-sm font-bold">{{ $title }}</span>
                            <span class="block text-[11px] text-muted">{{ $note }}</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ========================================================== category tiles --}}
<section class="border-b border-line bg-surface">
    <div class="ab-container py-5">
        <ul class="ab-scroll-x">
            @foreach ($categories as $category)
                <li class="shrink-0">
                    <a href="{{ route('site.accessories.shop', ['category' => $category['slug']]) }}"
                       class="flex w-24 flex-col items-center gap-1.5 rounded-xl border border-line px-2 py-3
                              text-center transition-colors hover:border-brand-400 hover:bg-brand-50">
                        <x-ui.product-art :art="$category['art']" class="h-10 w-auto" />
                        <span class="text-[10px] font-semibold leading-tight">{{ $category['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</section>

{{-- ========================================================= promo banners --}}
<section class="ab-container py-8">
    <ul class="grid gap-4 lg:grid-cols-3" data-reveal-group>
        @foreach ($promos as $promo)
            <li class="relative overflow-hidden rounded-xl bg-ink p-5 text-white" data-reveal>
                @if ($promo['badge'])
                    <span class="absolute right-4 top-4 grid h-14 w-14 place-items-center rounded-full
                                 bg-accent-500 text-center text-[9px] font-extrabold leading-tight text-ink">
                        {{ $promo['badge'] }}
                    </span>
                @endif

                <h2 class="max-w-[60%] text-lg font-extrabold leading-tight">{{ $promo['title'] }}</h2>
                <p class="mt-1 text-[11px] text-white/70">{{ $promo['meta'] }}</p>

                <p class="mt-3 text-[11px] text-white/70">From</p>
                <p class="text-2xl font-extrabold">₹{{ number_format($promo['from']) }}</p>

                <a href="{{ route('site.accessories.shop') }}"
                   class="ab-btn ab-btn-accent mt-3 px-4 py-2 text-xs">
                    Shop Now <x-ui.icon name="arrow-right" :size="14" />
                </a>

                @if (! empty($promo['bullets']))
                    <ul class="absolute bottom-5 right-5 hidden space-y-1.5 xl:block">
                        @foreach ($promo['bullets'] as $bullet)
                            <li class="flex items-center gap-1.5 text-[10px]">
                                <x-ui.icon name="check" :size="11" class="text-accent-500" /> {{ $bullet }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                <x-ui.product-art :art="$promo['art']"
                                  class="pointer-events-none absolute -bottom-2 right-4 h-24 w-auto text-white/15" />
            </li>
        @endforeach
    </ul>
</section>

{{-- ============================================ popular + installation block --}}
<section class="ab-container pb-10">
    <div class="grid gap-6 lg:grid-cols-12">

        <div class="min-w-0 lg:col-span-7">
            <x-ui.section-heading title="Popular Accessories"
                                  :href="route('site.accessories.shop')" />

            <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3" data-reveal-group>
                @foreach ($products as $product)
                    <li data-reveal><x-ui.product-card :product="$product" class="h-full" /></li>
                @endforeach
            </ul>
        </div>

        {{-- Professional installation --}}
        <div class="min-w-0 lg:col-span-5">
            <div class="ab-card h-full overflow-hidden">
                <div class="p-5">
                    <h2 class="flex items-center gap-2 text-base font-extrabold">
                        <x-ui.icon name="wrench" :size="19" class="text-brand-500" />
                        Professional Installation
                    </h2>
                    <p class="mt-1 text-xs text-muted">
                        Get expert installation for a hassle-free experience.
                    </p>

                    <ul class="mt-4 space-y-2">
                        @foreach ([
                            'Available in ' . implode(', ', $site['service_area']['districts']),
                            'Trained Technicians',
                            'Quick & Safe Installation',
                            'Genuine Products Only',
                        ] as $point)
                            <li class="flex items-start gap-2 text-xs">
                                <x-ui.icon name="check-circle" :size="14" class="mt-0.5 shrink-0 text-success" />
                                {{ $point }}
                            </li>
                        @endforeach
                    </ul>

                    <a href="{{ route('site.contact') }}" class="ab-btn ab-btn-primary mt-5 w-full text-xs">
                        Book Installation <x-ui.icon name="arrow-right" :size="14" />
                    </a>
                </div>

                <ul class="grid grid-cols-2 gap-2 border-t border-line bg-brand-50 p-4 text-center sm:grid-cols-4">
                    @foreach (['Accessories', 'Fitment', 'Servicing', 'Ready to Ride'] as $label)
                        <li class="text-[10px] font-semibold">
                            <x-ui.icon name="check" :size="14" class="mx-auto text-brand-500" />
                            <span class="mt-1 block">{{ $label }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

<x-ui.shop-promises :promises="$site['shop_promises']" />

@endsection
