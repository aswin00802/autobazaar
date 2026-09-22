@extends('site.layout')

@section('title', 'Order ' . $order['id'])
@section('robots', 'noindex, nofollow')

@section('content')

<x-ui.account-shell :account="$account" active="My Orders">

    <div class="grid gap-5 lg:grid-cols-12">

        {{-- ============================================================ main --}}
        <div class="min-w-0 space-y-5 lg:col-span-9">

            {{-- Confirmation banner --}}
            <div class="ab-card flex flex-wrap items-start justify-between gap-4 bg-brand-50 p-5">
                <div class="flex items-start gap-3">
                    <x-ui.icon name="check-circle" :size="30" class="shrink-0 text-success" />
                    <div>
                        <h1 class="text-lg font-extrabold">{{ $order['headline'] }}</h1>
                        <p class="mt-0.5 text-xs text-muted">{{ $order['subhead'] }}</p>
                    </div>
                </div>

                <div class="text-right leading-tight">
                    <p class="text-xs text-muted">Order ID: <span class="font-bold text-ink">{{ $order['id'] }}</span></p>
                    <p class="text-[11px] text-muted">Placed on: {{ $order['placed_at'] }}</p>
                </div>
            </div>

            {{-- Tracker --}}
            <div class="ab-card p-5">
                <h2 class="text-base font-extrabold">Order Tracking</h2>
                <p class="mt-0.5 text-xs text-muted">Track the status of your vehicle purchase in real time.</p>

                <ol class="mt-6 grid grid-cols-2 gap-y-6 sm:grid-cols-5">
                    @foreach ($order['tracker'] as $stage)
                        @php
                            $isDone = $stage['state'] === 'done';
                            $isCurrent = $stage['state'] === 'current';
                        @endphp
                        <li class="relative flex flex-col items-center px-1 text-center">

                            {{-- Connector: sits behind the marker, hidden on the last stage --}}
                            @unless ($loop->last)
                                <span class="absolute left-1/2 top-5 hidden h-0.5 w-full sm:block
                                             {{ $isDone ? 'bg-brand-500' : 'bg-line' }}"
                                      aria-hidden="true"></span>
                            @endunless

                            <span class="relative z-10 grid h-10 w-10 place-items-center rounded-full
                                         {{ $isDone ? 'bg-brand-500 text-white'
                                            : ($isCurrent ? 'bg-brand-500 text-white ring-4 ring-brand-100 ab-pulse'
                                                          : 'bg-line text-muted') }}">
                                <x-ui.icon :name="$stage['icon']" :size="18" />
                            </span>

                            <span class="mt-2 text-xs font-bold leading-tight">{{ $stage['label'] }}</span>

                            @if ($stage['note'])
                                <span class="mt-0.5 whitespace-pre-line text-[10px] leading-tight text-muted">{{ $stage['note'] }}</span>
                            @endif

                            <span class="sr-only">
                                {{ $isDone ? 'Completed' : ($isCurrent ? 'In progress' : 'Pending') }}
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Order details --}}
            <div class="ab-card p-5">
                <h2 class="mb-4 text-base font-extrabold">Order Details</h2>

                <div class="flex flex-col gap-5 sm:flex-row sm:items-center">
                    <img src="{{ asset($order['vehicle']['image']) }}" alt="{{ $order['vehicle']['name'] }}"
                         class="mx-auto h-32 w-auto shrink-0 object-contain sm:mx-0" loading="lazy">

                    <div class="flex-1">
                        <p class="flex flex-wrap items-center gap-2">
                            <span class="text-lg font-extrabold">{{ $order['vehicle']['name'] }}</span>
                            <span class="rounded-full bg-brand-50 px-2.5 py-0.5 text-[10px] font-bold text-brand-600">
                                {{ $order['vehicle']['tag'] }}
                            </span>
                        </p>
                        <p class="text-xs text-muted">{{ $order['vehicle']['meta'] }}</p>

                        <dl class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach ($order['vehicle']['facts'] as $fact)
                                <div class="flex items-start gap-2">
                                    <x-ui.icon :name="$fact['icon']" :size="17" class="mt-0.5 shrink-0 text-brand-500" />
                                    <span class="leading-tight">
                                        <dt class="block text-[10px] text-muted">{{ $fact['label'] }}</dt>
                                        <dd class="block text-xs font-bold">{{ $fact['value'] }}</dd>
                                    </span>
                                </div>
                            @endforeach
                        </dl>

                        <a href="{{ route('site.model', ['tvs', 'king-deluxe']) }}"
                           class="mt-4 inline-flex items-center gap-1.5 text-xs font-semibold text-brand-500 underline underline-offset-2">
                            View Full Specifications <x-ui.icon name="arrow-right" :size="13" />
                        </a>
                    </div>
                </div>
            </div>

            {{-- Delivery / payment / documents --}}
            <div class="grid gap-4 md:grid-cols-3">

                <div class="ab-card p-4">
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-extrabold">
                        <x-ui.icon name="truck" :size="17" class="text-brand-500" /> Delivery Details
                    </h3>

                    <div class="flex items-start gap-2.5">
                        <x-ui.icon name="clock" :size="15" class="mt-0.5 shrink-0 text-muted" />
                        <span class="leading-tight">
                            <span class="block text-[10px] text-muted">Expected Delivery</span>
                            <span class="block text-xs font-bold">{{ $order['delivery']['expected'] }}</span>
                        </span>
                    </div>

                    <div class="mt-3 flex items-start gap-2.5">
                        <x-ui.icon name="pin" :size="15" class="mt-0.5 shrink-0 text-muted" />
                        <span class="leading-tight">
                            <span class="block text-[10px] text-muted">Delivery Location</span>
                            @foreach ($order['delivery']['location'] as $line)
                                <span class="block text-xs font-bold">{{ $line }}</span>
                            @endforeach
                        </span>
                    </div>
                </div>

                <div class="ab-card p-4">
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-extrabold">
                        <x-ui.icon name="card" :size="17" class="text-brand-500" /> Payment Details
                    </h3>

                    <dl class="space-y-3">
                        @foreach ([
                            ['check', 'Payment Method', $order['payment']['method'], null],
                            ['doc', 'Amount Paid', $order['payment']['paid'], $order['payment']['paid_note']],
                            ['mail', 'Balance Amount', $order['payment']['balance'], $order['payment']['balance_note']],
                        ] as [$icon, $label, $value, $note])
                            <div class="flex items-start gap-2.5">
                                <x-ui.icon :name="$icon" :size="15" class="mt-0.5 shrink-0 text-muted" />
                                <span class="leading-tight">
                                    <dt class="block text-[10px] text-muted">{{ $label }}</dt>
                                    <dd class="block text-xs font-bold">
                                        {{ $value }}
                                        @if ($note)<span class="font-normal text-muted">{{ $note }}</span>@endif
                                    </dd>
                                </span>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="ab-card p-4">
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-extrabold">
                        <x-ui.icon name="doc" :size="17" class="text-brand-500" /> Documents
                    </h3>

                    <ul class="space-y-2">
                        @foreach ($order['documents'] as $doc)
                            <li class="flex items-center justify-between gap-2">
                                <span class="flex items-center gap-2 text-xs">
                                    <x-ui.icon name="doc" :size="14" class="text-muted" />
                                    {{ $doc['label'] }}
                                </span>

                                {{-- Prototype: no file behind this yet --}}
                                <button type="button"
                                        class="flex items-center gap-1 rounded border border-line px-2 py-1
                                               text-[10px] font-semibold transition-colors hover:bg-canvas">
                                    <x-ui.icon name="download" :size="12" /> Download
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        {{-- =========================================================== aside --}}
        <aside class="min-w-0 space-y-4 lg:col-span-3">

            <div class="relative overflow-hidden rounded-xl bg-brand-50 p-4">
                <img src="{{ asset($order['vehicle']['image']) }}" alt="" aria-hidden="true"
                     class="w-full max-w-48 object-contain" loading="lazy">
                <p class="ab-script mt-1 text-xl leading-tight text-brand-600">
                    Better Miles<br>Brighter Tomorrow
                </p>
            </div>

            <div class="ab-card p-4">
                <p class="mb-1 flex items-center gap-2 text-sm font-extrabold">
                    <x-ui.icon name="headset" :size="17" class="text-brand-500" /> Need Help?
                </p>
                <p class="text-[11px] text-muted">Our support team is here for you.</p>

                <ul class="mt-3 space-y-3">
                    @foreach ([
                        ['phone-call', 'Call Us', $site['contact']['phone']],
                        ['whatsapp', 'Chat on WhatsApp', $site['contact']['phone']],
                        ['mail', 'Email Us', $site['contact']['email']],
                        ['clock', 'Support Hours', $site['contact']['hours']],
                    ] as [$icon, $label, $value])
                        <li class="flex items-start gap-2.5">
                            <x-ui.icon :name="$icon" :size="16" class="mt-0.5 shrink-0 text-brand-500" />
                            <span class="leading-tight">
                                <span class="block text-[10px] text-muted">{{ $label }}</span>
                                <span class="block text-xs font-bold">{{ $value }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="ab-card bg-accent-50 p-4">
                <x-ui.icon name="handshake" :size="24" class="text-warn" />
                <p class="mt-2 text-sm font-extrabold">Thank You!</p>
                <p class="mt-0.5 text-[11px] text-muted">
                    For being a part of AutoBazaar Family.
                </p>
                <p class="mt-2 flex items-start gap-1.5 text-[11px] text-muted">
                    <x-ui.icon name="leaf" :size="14" class="mt-0.5 shrink-0 text-success" />
                    Together we move towards a cleaner, brighter tomorrow.
                </p>
            </div>
        </aside>
    </div>
</x-ui.account-shell>

{{-- ============================================================== trust bar --}}
<section class="border-t border-line bg-surface">
    <div class="ab-container grid gap-5 py-6 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['shield', '100% Genuine Vehicles', 'Trusted and Verified'],
            ['truck', 'Safe & Fast Delivery', 'Across ' . implode(', ', $site['service_area']['districts'])],
            ['rupee', 'Flexible Finance Options', 'EMI, Bank Loan & NBFC Support'],
            ['users', 'Dedicated Support', "We're with you at every step"],
        ] as [$icon, $title, $note])
            <div class="flex items-center gap-3">
                <x-ui.icon :name="$icon" :size="24" class="text-brand-500" />
                <span class="leading-tight">
                    <span class="block text-sm font-bold">{{ $title }}</span>
                    <span class="block text-[11px] text-muted">{{ $note }}</span>
                </span>
            </div>
        @endforeach
    </div>
</section>

@endsection
