@extends('site.layout')

@section('title', 'My Orders')

@section('content')

<x-ui.account-shell :account="$account" active="My Orders">

    <h1 class="text-xl font-extrabold">My Orders</h1>
    <p class="mt-1 text-sm text-muted">Vehicle bookings and accessory orders in one place.</p>

    {{-- Vehicle order --}}
    <h2 class="mt-6 mb-3 text-sm font-bold uppercase tracking-wide text-muted">Vehicle Orders</h2>

    <a href="{{ route('site.account.order', $order['id']) }}"
       class="ab-card flex flex-wrap items-center gap-5 p-5 ab-lift">

        <img src="{{ asset($order['vehicle']['image']) }}" alt="{{ $order['vehicle']['name'] }}"
             class="h-24 w-32 shrink-0 object-contain" loading="lazy">

        <div class="min-w-48 flex-1">
            <p class="text-xs text-muted">
                Order {{ $order['id'] }} · Placed {{ $order['placed_at'] }}
            </p>
            <p class="text-base font-extrabold">{{ $order['vehicle']['name'] }}</p>
            <p class="text-xs text-muted">{{ $order['vehicle']['meta'] }}</p>

            <span class="mt-2 inline-block rounded-full bg-accent-500 px-2.5 py-1 text-[10px] font-bold text-ink">
                {{ $order['status'] }}
            </span>
        </div>

        <div class="text-right">
            <p class="text-[11px] text-muted">Amount Paid</p>
            <p class="text-sm font-bold">{{ $order['payment']['paid'] }} {{ $order['payment']['paid_note'] }}</p>
            <span class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-brand-500">
                View Details <x-ui.icon name="arrow-right" :size="13" />
            </span>
        </div>
    </a>

    {{-- Accessory orders --}}
    <h2 class="mt-8 mb-3 text-sm font-bold uppercase tracking-wide text-muted">Accessory Orders</h2>

    <div class="ab-card p-8 text-center">
        <x-ui.icon name="cart" :size="30" class="mx-auto text-line" />
        <p class="mt-3 text-sm font-semibold">No accessory orders yet</p>
        <p class="mt-1 text-xs text-muted">Anything you order from the shop will appear here.</p>
        <a href="{{ route('site.accessories.shop') }}" class="ab-btn ab-btn-primary mt-4 text-xs">
            Browse Accessories <x-ui.icon name="arrow-right" :size="14" />
        </a>
    </div>
</x-ui.account-shell>

@endsection
