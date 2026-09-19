@extends('site.layout')

@section('title', 'My Account')

@section('content')

<x-ui.account-shell :account="$account" active="Dashboard">

    <h1 class="text-xl font-extrabold">Welcome back, {{ $firstName }}</h1>
    <p class="mt-1 text-sm text-muted">Here is what is happening with your account.</p>

    {{-- Stat tiles — every number is the signed-in customer's own --}}
    <ul class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach ([
            ['doc', $stats['orders'], 'Orders', route('site.account.orders'), 'brand'],
            ['message', $stats['enquiries'], 'Enquiries', route('site.account.section', 'enquiries'), 'info'],
            ['pin', $stats['addresses'], 'Saved Addresses', route('site.account.section', 'addresses'), 'violet'],
            ['cart', $stats['cart'], 'Items in Cart', route('site.cart'), 'accent'],
        ] as [$icon, $value, $label, $href, $tone])
            <li>
                <a href="{{ $href }}" class="ab-card flex items-center gap-3 p-4 ab-lift">
                    <x-ui.tone-icon :icon="$icon" :tone="$tone" :size="20" shape="circle" />
                    <span class="leading-tight">
                        <span class="block text-xl font-extrabold">{{ $value }}</span>
                        <span class="block text-[11px] text-muted">{{ $label }}</span>
                    </span>
                </a>
            </li>
        @endforeach
    </ul>

    {{-- Latest order --}}
    <h2 class="mt-8 mb-3 text-base font-extrabold">Your Latest Order</h2>

    @if ($latestOrder)
        <x-ui.order-row :order="$latestOrder" />
    @else
        <div class="ab-card p-8 text-center">
            <x-ui.icon name="cart" :size="30" class="mx-auto text-line" />
            <p class="mt-3 text-sm font-semibold">No orders yet</p>
            <p class="mt-1 text-xs text-muted">Anything you order from the accessories shop will appear here.</p>
            <a href="{{ route('site.accessories.shop') }}" class="ab-btn ab-btn-primary mt-4 text-xs">
                Browse Accessories <x-ui.icon name="arrow-right" :size="14" />
            </a>
        </div>
    @endif

    {{-- Shortcuts --}}
    <h2 class="mt-8 mb-3 text-base font-extrabold">Quick Actions</h2>

    <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['scales', 'Compare Models', 'site.compare'],
            ['calculator', 'Calculate EMI', 'site.finance'],
            ['cart', 'Shop Accessories', 'site.accessories.shop'],
            ['headset', 'Contact Support', 'site.contact'],
        ] as [$icon, $label, $route])
            <li>
                <a href="{{ route($route) }}"
                   class="ab-card flex items-center gap-3 p-4 text-sm font-semibold ab-lift">
                    <x-ui.icon :name="$icon" :size="18" class="text-brand-500" />
                    {{ $label }}
                    <x-ui.icon name="arrow-right" :size="15" class="ml-auto text-muted" />
                </a>
            </li>
        @endforeach
    </ul>
</x-ui.account-shell>

@endsection
