@extends('site.layout')

@section('title', 'Finance & EMI')
@section('description', 'Calculate your EMI, estimate running cost and explore bank and NBFC finance options.')

@section('content')

@php $lead = $vehicles[0]; @endphp

<x-ui.page-hero title="Finance &amp; EMI"
                lede="Work out what your autorickshaw actually costs — monthly EMI, daily running cost, and the finance options open to you."
                :breadcrumb="[
                    ['label' => 'Home', 'href' => route('site.home')],
                    ['label' => 'Finance & EMI'],
                ]" />

<section class="ab-container py-8">

    {{-- Calculators --}}
    <div class="grid gap-5 lg:grid-cols-2" data-reveal-group>
        <x-ui.emi-panel :vehicle="$lead" />
        <x-ui.operating-cost-panel :vehicle="$lead" />
    </div>

    {{-- Finance options --}}
    <x-ui.section-heading title="Your Finance Options" class="mt-10" />

    <ul class="grid gap-4 md:grid-cols-2 xl:grid-cols-4" data-reveal-group>
        @foreach ([
            ['rupee', 'Net Cash Purchase', 'Pay in full and avoid interest entirely. Best if you have the capital available.', 'brand'],
            ['bank', 'Bank Loan', 'Nationalised and private banks, typically 9–11% for buyers with a clean record.', 'info'],
            ['handshake', 'Private Finance', 'Faster approval and lighter paperwork, at a higher effective rate.', 'accent'],
            ['tag', 'Exchange Offer', 'Trade in your existing auto and reduce the amount you need to finance.', 'violet'],
        ] as [$icon, $title, $body, $tone])
            <li class="ab-card flex h-full flex-col p-5">
                <x-ui.tone-icon :icon="$icon" :tone="$tone" :size="20" shape="circle" />
                <h3 class="mt-3 text-sm font-bold">{{ $title }}</h3>
                <p class="mt-1.5 flex-1 text-xs leading-relaxed text-muted">{{ $body }}</p>
                <a href="{{ route('site.enquiry') }}"
                   class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-brand-500 underline underline-offset-2">
                    Enquire <x-ui.icon name="arrow-right" :size="13" />
                </a>
            </li>
        @endforeach
    </ul>

    {{-- EMI across the range --}}
    <x-ui.section-heading title="Approximate EMI Across the Range" class="mt-10"
                          lede="Estimated for a 3-year term with a 20% down payment."
                          :href="route('site.compare')" link-label="Compare Models" />

    <div class="ab-card overflow-x-auto" data-reveal>
        <table class="w-full min-w-xl border-collapse text-sm">
            <thead>
                <tr class="border-b border-line text-left text-xs text-muted">
                    <th scope="col" class="p-3 font-semibold">Model</th>
                    <th scope="col" class="p-3 font-semibold">Ex-Showroom</th>
                    <th scope="col" class="p-3 font-semibold">Approx. EMI</th>
                    <th scope="col" class="p-3 font-semibold">Monthly Fuel</th>
                    <th scope="col" class="p-3 font-semibold"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vehicles as $vehicle)
                    <tr class="border-b border-line last:border-0">
                        <td class="p-3">
                            <span class="flex items-center gap-2.5">
                                <img src="{{ asset($vehicle['image']) }}" alt="" aria-hidden="true"
                                     class="h-9 w-12 object-contain" loading="lazy">
                                <span class="font-semibold">{{ $vehicle['name'] }}</span>
                            </span>
                        </td>
                        <td class="p-3">{{ $vehicle['compare']['ex_showroom_label'] }}</td>
                        <td class="p-3 font-bold">₹{{ number_format($vehicle['compare']['emi']) }}/month</td>
                        <td class="p-3">₹{{ number_format($vehicle['compare']['monthly_fuel']) }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('site.model', [$vehicle['brand_slug'], $vehicle['model_slug']]) }}"
                               class="text-xs font-semibold text-brand-500 underline underline-offset-2">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- CTA --}}
    <div class="ab-card mt-8 flex flex-wrap items-center justify-between gap-4 bg-brand-50 p-5">
        <div>
            <p class="text-base font-extrabold">Not sure which option suits you?</p>
            <p class="mt-0.5 text-xs text-muted">
                Tell us your budget and usage — we will work out the best route for your location.
            </p>
        </div>
        <a href="{{ route('site.enquiry') }}" class="ab-btn ab-btn-primary">
            Get Buying Assistance <x-ui.icon name="arrow-right" :size="16" />
        </a>
    </div>
</section>

@endsection
