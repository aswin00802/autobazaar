@extends('site.layout')

@section('title', 'Frequently Asked Questions')

@section('content')

@php
    $faqs = [
        'Buying' => [
            ['Where can I buy directly from AutoBazaar?',
             'Direct vehicle purchase is available in ' . implode(', ', $site['service_area']['districts']) . '. For other districts in Tamil Nadu we provide buying assistance, and for other states we connect you with a suitable nearby dealer.'],
            ['Is the price shown the on-road price?',
             'On a model page we show the full breakup — ex-showroom, RTO charges, insurance, registration and handling, and other charges — which adds up to the on-road price for your selected location. Prices vary by district.'],
            ['Can I book a vehicle online?',
             'Yes. In our direct purchase districts you can book with an advance payment and track the order through to delivery from your account.'],
        ],
        'Finance' => [
            ['What EMI can I expect?',
             'Use the EMI calculator on any model page. Rates for three-wheeler loans typically sit around 9–11% depending on your repayment record, with tenures from 12 to 60 months.'],
            ['What buying options do you support?',
             'Net cash purchase, private finance, bank loan and exchange offers. Tell us your preference on the enquiry form and we will work through the options with you.'],
            ['Do you help with government schemes?',
             'Yes. Our Government Schemes page lists live subsidies, loan schemes and scrappage benefits, and our team will help you check eligibility and prepare documents.'],
        ],
        'Accessories' => [
            ['Do you deliver accessories across India?',
             'Yes, all-India delivery via Shiprocket. Standard delivery is free on orders above ₹999 and takes 3–6 business days; express delivery is ₹69 and takes 1–3 business days.'],
            ['Can I get accessories fitted?',
             'Professional fitment is available in our four direct-service districts. Choose Book Installation on the accessories page and we will arrange a slot.'],
            ['What is your returns policy?',
             'Unused items in original packaging can be returned within the window set out in our Returns & Refunds policy. Raise a request from your order and we will arrange collection.'],
        ],
    ];
@endphp

<x-ui.page-hero title="Frequently Asked Questions"
                lede="Buying, finance and accessories — the questions drivers ask us most."
                :breadcrumb="[
                    ['label' => 'Home', 'href' => route('site.home')],
                    ['label' => 'FAQ'],
                ]" />

<section class="ab-container py-10">
    <div class="grid gap-8 lg:grid-cols-12">

        <div class="min-w-0 space-y-8 lg:col-span-8">
            @foreach ($faqs as $group => $items)
                <div>
                    <h2 class="mb-3 text-lg font-extrabold">{{ $group }}</h2>

                    <ul class="space-y-2">
                        @foreach ($items as $i => [$question, $answer])
                            <li class="ab-card overflow-hidden" x-data="{ open: {{ $loop->parent->first && $i === 0 ? 'true' : 'false' }} }">
                                <h3>
                                    <button type="button" @click="open = !open"
                                            :aria-expanded="open"
                                            class="flex w-full items-center justify-between gap-3 p-4 text-left">
                                        <span class="text-sm font-semibold">{{ $question }}</span>
                                        <x-ui.icon name="chevron-down" :size="17"
                                                   class="shrink-0 text-muted transition-transform"
                                                   ::class="open && 'rotate-180'" />
                                    </button>
                                </h3>

                                {{-- x-show + opacity transition rather than the
                                     Collapse plugin, to keep the bundle small --}}
                                <div x-show="open" x-cloak x-transition.opacity.duration.150ms>
                                    <p class="border-t border-line p-4 text-sm leading-relaxed text-ink-soft">
                                        {{ $answer }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>

        <aside class="min-w-0 lg:col-span-4">
            <div class="ab-card p-5">
                <p class="flex items-center gap-2 text-sm font-extrabold">
                    <x-ui.icon name="headset" :size="17" class="text-brand-500" />
                    Still have a question?
                </p>
                <p class="mt-1 text-xs text-muted">
                    Our team answers {{ $site['contact']['hours'] }}.
                </p>

                <a href="https://wa.me/{{ $site['contact']['whatsapp'] }}" target="_blank" rel="noopener"
                   class="ab-btn ab-btn-primary mt-4 w-full text-xs">
                    <x-ui.icon name="whatsapp" :size="16" /> Chat on WhatsApp
                </a>

                <a href="{{ route('site.contact') }}" class="ab-btn ab-btn-ghost mt-2 w-full text-xs">
                    Send a Message
                </a>
            </div>
        </aside>
    </div>
</section>

@endsection
