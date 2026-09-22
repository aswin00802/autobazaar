@extends('site.layout')

@section('title', 'My Orders')
@section('robots', 'noindex, nofollow')

@section('content')

<x-ui.account-shell :account="$account" active="My Orders">

    <h1 class="text-xl font-extrabold">My Orders</h1>
    <p class="mt-1 text-sm text-muted">Everything you have ordered from the accessories shop.</p>

    @if ($orders->isEmpty())
        <div class="ab-card mt-6 p-8 text-center">
            <x-ui.icon name="cart" :size="30" class="mx-auto text-line" />
            <p class="mt-3 text-sm font-semibold">No orders yet</p>
            <p class="mt-1 text-xs text-muted">Anything you order from the shop will appear here.</p>
            <a href="{{ route('site.accessories.shop') }}" class="ab-btn ab-btn-primary mt-4 text-xs">
                Browse Accessories <x-ui.icon name="arrow-right" :size="14" />
            </a>
        </div>
    @else
        <ul class="mt-6 space-y-3">
            @foreach ($orders as $order)
                <li><x-ui.order-row :order="$order" /></li>
            @endforeach
        </ul>
    @endif
</x-ui.account-shell>

@endsection
