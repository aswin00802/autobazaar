@extends('site.layout')

@section('title', 'Your Cart')

@section('content')

<section class="ab-container py-6">

    <x-ui.breadcrumb :items="[
        ['label' => 'Home', 'href' => route('site.home')],
        ['label' => 'Accessories Shop', 'href' => route('site.accessories.shop')],
        ['label' => 'Cart'],
    ]" />

    @if (session('success'))
        <p class="mt-4 flex items-center gap-2 rounded-lg bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-600">
            <x-ui.icon name="check-circle" :size="17" /> {{ session('success') }}
        </p>
    @endif
    @if (session('error'))
        <p class="mt-4 flex items-center gap-2 rounded-lg bg-red-50 px-4 py-3 text-sm font-semibold text-danger">
            <x-ui.icon name="warning" :size="17" /> {{ session('error') }}
        </p>
    @endif

    @if (empty($totals['lines']))
        <div class="ab-card mt-5 p-12 text-center">
            <x-ui.icon name="cart" :size="40" class="mx-auto text-line" />
            <p class="mt-4 text-lg font-extrabold">Your cart is empty</p>
            <p class="mt-1 text-sm text-muted">Browse the accessories shop and add something to get started.</p>
            <a href="{{ route('site.accessories.shop') }}" class="ab-btn ab-btn-primary mt-5">
                Shop Accessories <x-ui.icon name="arrow-right" :size="16" />
            </a>
        </div>
    @else
        <div class="mt-5 grid gap-5 lg:grid-cols-12">
            <div class="min-w-0 lg:col-span-8">
                @include('site.partials.cart-card')
            </div>

            <aside class="min-w-0 lg:col-span-4" aria-label="Order summary">
                <div class="ab-card p-5 lg:sticky lg:top-32">
                    <h2 class="mb-3 flex items-center gap-2 text-base font-extrabold">
                        <x-ui.icon name="doc" :size="18" class="text-brand-500" /> Order Summary
                    </h2>

                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-muted">Subtotal ({{ $totals['item_count'] }} items)</dt>
                            <dd class="font-semibold">₹{{ number_format($totals['subtotal']) }}</dd>
                        </div>
                        @if ($totals['discount'] > 0)
                            <div class="flex justify-between">
                                <dt class="text-success">Discount ({{ $totals['coupon_code'] }})</dt>
                                <dd class="font-semibold text-success">− ₹{{ number_format($totals['discount']) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between">
                            <dt class="text-muted">Delivery</dt>
                            <dd class="font-semibold">{{ $totals['shipping'] > 0 ? '₹' . number_format($totals['shipping']) : 'Free' }}</dd>
                        </div>
                        <div class="flex justify-between border-t border-line pt-2 text-base">
                            <dt class="font-extrabold">Total</dt>
                            <dd class="font-extrabold text-brand-600">₹{{ number_format($totals['total']) }}</dd>
                        </div>
                    </dl>

                    <a href="{{ route('site.checkout') }}" class="ab-btn ab-btn-primary mt-4 w-full">
                        Proceed to Checkout <x-ui.icon name="arrow-right" :size="16" />
                    </a>

                    @guest
                        <p class="mt-2 text-center text-[11px] text-muted">
                            You will be asked to log in with your mobile number. Your cart is kept.
                        </p>
                    @endguest

                    <a href="{{ route('site.accessories.shop') }}"
                       class="mt-3 block text-center text-xs font-semibold text-brand-500 underline underline-offset-2">
                        Continue Shopping
                    </a>
                </div>
            </aside>
        </div>
    @endif
</section>

@endsection
