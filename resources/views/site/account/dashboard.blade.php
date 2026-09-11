@extends('site.layout')

@section('title', 'My Account')

@section('content')

<x-ui.account-shell :account="$account" active="Dashboard">

    <h1 class="text-xl font-extrabold">Welcome back, {{ explode(' ', $account['user']['name'])[0] }}</h1>
    <p class="mt-1 text-sm text-muted">Here is what is happening with your account.</p>

    {{-- Stat tiles --}}
    <ul class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['doc', '1', 'Active Order', 'site.account.orders', 'brand'],
            ['doc', '3', 'Enquiries', 'site.account.section', 'info', 'enquiries'],
            ['heart', '4', 'Saved Vehicles', 'site.account.section', 'danger', 'saved'],
            ['chart', '2', 'Comparisons', 'site.account.section', 'violet', 'comparisons'],
        ] as $tile)
            @php
                [$icon, $value, $label, $route, $tone] = $tile;
                $param = $tile[5] ?? null;
                $href = $param ? route($route, $param) : route($route);
            @endphp
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

    {{-- Active order --}}
    <h2 class="mt-8 mb-3 text-base font-extrabold">Your Active Order</h2>

    <a href="{{ route('site.account.order', $order['id']) }}"
       class="ab-card flex flex-wrap items-center gap-5 p-5 ab-lift">

        <img src="{{ asset($order['vehicle']['image']) }}" alt="{{ $order['vehicle']['name'] }}"
             class="h-24 w-32 shrink-0 object-contain" loading="lazy">

        <div class="min-w-48 flex-1">
            <p class="text-xs text-muted">Order {{ $order['id'] }}</p>
            <p class="text-base font-extrabold">{{ $order['vehicle']['name'] }}</p>
            <p class="text-xs text-muted">{{ $order['vehicle']['meta'] }}</p>

            <span class="mt-2 inline-block rounded-full bg-accent-500 px-2.5 py-1 text-[10px] font-bold text-ink">
                {{ $order['status'] }}
            </span>
        </div>

        <div class="text-right">
            <p class="text-[11px] text-muted">Expected Delivery</p>
            <p class="text-sm font-bold">{{ $order['delivery']['expected'] }}</p>
            <span class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-brand-500">
                Track Order <x-ui.icon name="arrow-right" :size="13" />
            </span>
        </div>
    </a>

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
