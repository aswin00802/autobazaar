@extends('site.layout')

@section('title', 'About Us')
@section('description', 'AutoBazaar is a trusted autorickshaw marketplace helping drivers compare, finance and buy with confidence.')

@section('content')

<x-ui.page-hero title="About AutoBazaar"
                lede="We help auto drivers choose the right vehicle, understand what it will really cost, and buy it with confidence."
                :breadcrumb="[
                    ['label' => 'Home', 'href' => route('site.home')],
                    ['label' => 'About Us'],
                ]" />

<section class="ab-container py-10">
    <div class="grid gap-10 lg:grid-cols-12">

        <div class="min-w-0 lg:col-span-7">
            <h2 class="text-xl font-extrabold">Why we exist</h2>

            <div class="mt-3 space-y-4 text-sm leading-relaxed text-ink-soft">
                <p>
                    Buying an autorickshaw is one of the largest financial decisions a driver makes, and
                    the information around it is scattered — prices vary by district, finance terms are
                    opaque, and running costs are rarely spelled out before the sale.
                </p>
                <p>
                    AutoBazaar brings the whole picture into one place: real specifications, on-road price
                    broken down line by line, EMI and running-cost calculators, live government schemes,
                    and honest comparison across every brand in the segment.
                </p>
                <p>
                    We sell directly in {{ implode(', ', array_slice($site['service_area']['districts'], 0, 3)) }}
                    and {{ end($site['service_area']['districts']) }}. Everywhere else in Tamil Nadu we provide
                    buying assistance, and across the rest of India we connect you with a suitable nearby dealer.
                </p>
            </div>

            <h2 class="mt-8 text-xl font-extrabold">What we stand for</h2>

            <ul class="mt-4 grid gap-4 sm:grid-cols-2" data-reveal-group>
                @foreach ([
                    ['shield', 'Genuine information', 'Specifications and prices we can stand behind, updated as they change.'],
                    ['rupee', 'Transparent pricing', 'The full on-road breakup, not a headline number with surprises later.'],
                    ['users', 'Driver first', 'Advice shaped around how you actually earn, not around what is easiest to sell.'],
                    ['leaf', 'A cleaner road ahead', 'Clear guidance on electric options and the schemes that support them.'],
                ] as [$icon, $title, $body])
                    <li class="ab-card p-4">
                        <x-ui.tone-icon :icon="$icon" tone="brand" :size="18" shape="circle" />
                        <h3 class="mt-2.5 text-sm font-bold">{{ $title }}</h3>
                        <p class="mt-1 text-xs leading-relaxed text-muted">{{ $body }}</p>
                    </li>
                @endforeach
            </ul>
        </div>

        <aside class="min-w-0 space-y-4 lg:col-span-5">
            <div class="ab-card overflow-hidden">
                <img src="{{ asset('assets/image/auto_brands/tvs.png') }}" alt=""
                     aria-hidden="true" class="w-full bg-brand-50 object-contain p-8" loading="lazy">

                <ul class="grid grid-cols-2 gap-4 p-5">
                    @foreach ($site['stats'] as $stat)
                        <li class="flex items-center gap-2.5">
                            <x-ui.icon :name="$stat['icon']" :size="22" class="text-brand-500" />
                            <span class="leading-tight">
                                <span class="block text-sm font-extrabold">{{ $stat['value'] }}</span>
                                <span class="block text-[10px] text-muted">{{ $stat['label'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="ab-card bg-brand-50 p-5">
                <p class="text-sm font-extrabold">Talk to us</p>
                <p class="mt-1 text-xs text-muted">
                    We are on the phone and on WhatsApp {{ $site['contact']['hours'] }}.
                </p>
                <a href="{{ route('site.contact') }}" class="ab-btn ab-btn-primary mt-3 w-full text-xs">
                    Contact AutoBazaar <x-ui.icon name="arrow-right" :size="14" />
                </a>
            </div>
        </aside>
    </div>
</section>

@endsection
