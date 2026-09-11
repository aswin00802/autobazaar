@extends('site.layout')

@section('title', 'Order ' . $order->order_number)

@section('content')

@php
    // The five stages of an accessory order, mapped from Order::FLOW.
    $stageMeta = [
        'placed'    => ['label' => 'Order Placed',    'icon' => 'check'],
        'confirmed' => ['label' => 'Payment Confirmed', 'icon' => 'check'],
        'packed'    => ['label' => 'Packed',          'icon' => 'box'],
        'shipped'   => ['label' => 'Shipped',         'icon' => 'truck'],
        'delivered' => ['label' => 'Delivered',       'icon' => 'check-circle'],
    ];
    $current = $order->stage_index;
    $historyByStatus = $order->history->keyBy('order_status');
@endphp

<section class="ab-container py-8">

    {{-- ========================================================= confirmation --}}
    <div class="ab-card flex flex-wrap items-start justify-between gap-4 bg-brand-50 p-5">
        <div class="flex items-start gap-3">
            <x-ui.icon name="check-circle" :size="30" class="shrink-0 text-success" />
            <div>
                <h1 class="text-lg font-extrabold">Your order is confirmed!</h1>
                <p class="mt-0.5 text-xs text-muted">
                    Thank you for choosing AutoBazaar. We will keep you updated at every step.
                </p>
            </div>
        </div>

        <div class="text-right leading-tight">
            <p class="text-xs text-muted">
                Order ID: <span class="font-bold text-ink">{{ $order->order_number }}</span>
            </p>
            <p class="text-[11px] text-muted">
                Placed on: {{ $order->created_at->format('d M Y, g:i A') }}
            </p>
        </div>
    </div>

    <div class="mt-5 grid gap-5 lg:grid-cols-12">

        <div class="space-y-5 lg:col-span-8">

            {{-- Tracker --}}
            <div class="ab-card p-5">
                <h2 class="text-base font-extrabold">Order Tracking</h2>
                <p class="mt-0.5 text-xs text-muted">Track the status of your order in real time.</p>

                <ol class="mt-6 grid grid-cols-2 gap-y-6 sm:grid-cols-5">
                    @foreach (\App\Models\Shop\Order::FLOW as $i => $status)
                        @php
                            $meta = $stageMeta[$status];
                            $isDone = $i < $current;
                            $isCurrent = $i === $current;
                            $at = $historyByStatus[$status]->created_at ?? null;
                        @endphp
                        <li class="relative flex flex-col items-center px-1 text-center">
                            @unless ($loop->last)
                                <span class="absolute left-1/2 top-5 hidden h-0.5 w-full sm:block
                                             {{ $isDone ? 'bg-brand-500' : 'bg-line' }}" aria-hidden="true"></span>
                            @endunless

                            <span class="relative z-10 grid h-10 w-10 place-items-center rounded-full
                                         {{ $isDone ? 'bg-brand-500 text-white'
                                            : ($isCurrent ? 'bg-brand-500 text-white ring-4 ring-brand-100 ab-pulse'
                                                          : 'bg-line text-muted') }}">
                                <x-ui.icon :name="$meta['icon']" :size="18" />
                            </span>

                            <span class="mt-2 text-xs font-bold leading-tight">{{ $meta['label'] }}</span>

                            @if ($at)
                                <span class="mt-0.5 text-[10px] leading-tight text-muted">
                                    {{ $at->format('d M, g:i A') }}
                                </span>
                            @endif

                            <span class="sr-only">
                                {{ $isDone ? 'Completed' : ($isCurrent ? 'In progress' : 'Pending') }}
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>

            {{-- Items --}}
            <div class="ab-card p-5">
                <h2 class="mb-4 text-base font-extrabold">
                    Order Details
                    <span class="text-sm font-normal text-muted">({{ $order->item_count }} items)</span>
                </h2>

                <ul class="divide-y divide-line">
                    @foreach ($order->items as $item)
                        <li class="flex items-center gap-4 py-3">
                            <span class="grid h-14 w-14 shrink-0 place-items-center rounded-lg bg-canvas">
                                @if ($item->product_image)
                                    <img src="{{ asset($item->product_image) }}" alt="" aria-hidden="true"
                                         class="h-11 w-11 object-contain">
                                @else
                                    <x-ui.icon name="box" :size="22" class="text-muted" />
                                @endif
                            </span>

                            <span class="min-w-0 flex-1 leading-tight">
                                <span class="block text-sm font-bold">{{ $item->product_name }}</span>
                                <span class="block text-xs text-muted">
                                    Qty {{ $item->qty }} × ₹{{ number_format($item->unit_price) }}
                                    @if ($item->brand_name) · Fits {{ $item->brand_name }} @endif
                                </span>
                            </span>

                            <span class="text-sm font-extrabold">₹{{ number_format($item->line_total) }}</span>
                        </li>
                    @endforeach
                </ul>

                <dl class="mt-4 space-y-2 border-t border-line pt-4 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-muted">Subtotal</dt>
                        <dd class="font-semibold">₹{{ number_format($order->subtotal) }}</dd>
                    </div>
                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between">
                            <dt class="text-success">Discount ({{ $order->coupon_code }})</dt>
                            <dd class="font-semibold text-success">− ₹{{ number_format($order->discount_amount) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-muted">Shipping</dt>
                        <dd class="font-semibold">
                            {{ $order->shipping_amount > 0 ? '₹' . number_format($order->shipping_amount) : 'Free' }}
                        </dd>
                    </div>
                    <div class="flex justify-between border-t border-line pt-2 text-base">
                        <dt class="font-extrabold">Total Paid</dt>
                        <dd class="font-extrabold">₹{{ number_format($order->total_amount) }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Delivery + payment --}}
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="ab-card p-4">
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-extrabold">
                        <x-ui.icon name="truck" :size="17" class="text-brand-500" /> Delivery Details
                    </h3>
                    <p class="text-xs font-bold">{{ $order->shipping_name }}</p>
                    <p class="text-xs text-muted">{{ $order->shipping_mobile }}</p>
                    <p class="mt-1.5 text-xs text-muted">
                        {{ $order->shipping_address_line_1 }}<br>
                        @if ($order->shipping_address_line_2){{ $order->shipping_address_line_2 }}<br>@endif
                        {{ collect([$order->shipping_city, $order->shipping_pincode])->filter()->implode(' - ') }}<br>
                        {{ $order->shipping_state }}
                    </p>
                    <p class="mt-2 text-[11px] text-muted">
                        {{ \App\Services\CartService::DELIVERY[$order->delivery_option]['label'] ?? 'Standard Delivery' }}
                        · {{ \App\Services\CartService::DELIVERY[$order->delivery_option]['note'] ?? '' }}
                    </p>
                </div>

                <div class="ab-card p-4">
                    <h3 class="mb-3 flex items-center gap-2 text-sm font-extrabold">
                        <x-ui.icon name="card" :size="17" class="text-brand-500" /> Payment Details
                    </h3>
                    <dl class="space-y-2 text-xs">
                        <div class="flex justify-between">
                            <dt class="text-muted">Method</dt>
                            <dd class="font-bold uppercase">{{ $order->payment_mode }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Status</dt>
                            <dd class="font-bold">{{ Str::headline($order->payment_status) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-muted">Amount</dt>
                            <dd class="font-bold">₹{{ number_format($order->total_amount) }}</dd>
                        </div>
                    </dl>

                    @if ($order->payment_status === 'pending')
                        <p class="mt-3 rounded-lg bg-accent-50 px-3 py-2 text-[11px] text-ink-soft">
                            Online payment is not yet connected in this build — the order is recorded
                            and our team will contact you to complete payment.
                        </p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Aside --}}
        <aside class="space-y-4 lg:col-span-4">
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

            <a href="{{ route('site.accessories.shop') }}" class="ab-btn ab-btn-outline w-full">
                Continue Shopping <x-ui.icon name="arrow-right" :size="16" />
            </a>
        </aside>
    </div>
</section>

<x-ui.shop-promises :promises="$site['shop_promises']" />

@endsection
