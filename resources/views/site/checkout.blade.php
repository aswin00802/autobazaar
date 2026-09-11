@extends('site.layout')

@section('title', 'Cart & Checkout')

@section('content')

@php
    $lines = $totals['lines'];
    $delivery = $totals['delivery_option'];
    $steps = [
        1 => 'Cart', 2 => 'Address', 3 => 'Delivery', 4 => 'Payment', 5 => 'Order Confirmed',
    ];
    // How far the visitor has actually got, so the stepper is honest.
    $currentStep = empty($lines) ? 1 : ($addresses->isEmpty() ? 2 : 4);
@endphp

<section class="ab-container py-6">

    {{-- ============================================================== flashes --}}
    @if (session('success'))
        <p class="mb-4 flex items-center gap-2 rounded-lg bg-brand-50 px-4 py-3 text-sm font-semibold text-brand-600">
            <x-ui.icon name="check-circle" :size="17" /> {{ session('success') }}
        </p>
    @endif
    @if (session('error'))
        <p class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 px-4 py-3 text-sm font-semibold text-danger">
            <x-ui.icon name="warning" :size="17" /> {{ session('error') }}
        </p>
    @endif

    {{-- ================================================================ steps --}}
    <ol class="mb-6 flex items-center justify-between gap-1 overflow-x-auto">
        @foreach ($steps as $n => $label)
            <li class="flex flex-1 items-center gap-2">
                <span class="grid h-7 w-7 shrink-0 place-items-center rounded-full text-xs font-bold
                             {{ $n <= $currentStep ? 'bg-brand-500 text-white' : 'bg-line text-muted' }}">
                    {{ $n }}
                </span>
                <span class="whitespace-nowrap text-xs font-semibold {{ $n <= $currentStep ? 'text-ink' : 'text-muted' }}">
                    {{ $label }}
                </span>
                @unless ($loop->last)
                    <span class="mx-1 hidden h-px flex-1 bg-line sm:block" aria-hidden="true"></span>
                @endunless
            </li>
        @endforeach
    </ol>

    @if (empty($lines))
        {{-- ========================================================= empty cart --}}
        <div class="ab-card p-12 text-center">
            <x-ui.icon name="cart" :size="40" class="mx-auto text-line" />
            <p class="mt-4 text-lg font-extrabold">Your cart is empty</p>
            <p class="mt-1 text-sm text-muted">Browse the accessories shop and add something to get started.</p>
            <a href="{{ route('site.accessories.shop') }}" class="ab-btn ab-btn-primary mt-5">
                Shop Accessories <x-ui.icon name="arrow-right" :size="16" />
            </a>
        </div>
    @else

    <div class="grid gap-5 lg:grid-cols-12">

        {{-- ================================================================ cart --}}
        <div class="lg:col-span-5">
            <div class="ab-card p-5">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <h1 class="flex items-center gap-2 text-lg font-extrabold">
                        <x-ui.icon name="cart" :size="20" class="text-brand-500" />
                        Your Cart
                        <span class="text-sm font-normal text-muted">({{ $totals['item_count'] }} Items)</span>
                    </h1>

                    <a href="{{ route('site.accessories.shop') }}"
                       class="flex items-center gap-1 whitespace-nowrap text-xs font-semibold text-brand-500 underline underline-offset-2">
                        Continue Shopping <x-ui.icon name="arrow-right" :size="13" />
                    </a>
                </div>

                <ul class="divide-y divide-line">
                    @foreach ($lines as $line)
                        <li class="grid gap-3 py-3.5 sm:grid-cols-[1fr_auto_auto_auto] sm:items-center">

                            <div class="flex items-center gap-3">
                                <span class="grid h-14 w-14 shrink-0 place-items-center rounded-lg bg-canvas">
                                    @if ($line['image'])
                                        <img src="{{ asset($line['image']) }}" alt="" aria-hidden="true"
                                             class="h-11 w-11 object-contain">
                                    @else
                                        <x-ui.product-art :art="$line['art']" class="h-9 w-auto" />
                                    @endif
                                </span>
                                <span class="min-w-0 leading-tight">
                                    <span class="block text-sm font-bold">{{ $line['name'] }}</span>
                                    @if ($line['subtitle'])
                                        <span class="block text-xs text-muted">{{ $line['subtitle'] }}</span>
                                    @endif
                                    <span class="mt-0.5 block text-[11px] text-muted">
                                        ₹{{ number_format($line['price']) }} each
                                        @if ($line['brand']) · Fits {{ $line['brand'] }} @endif
                                    </span>
                                </span>
                            </div>

                            {{-- Quantity. Plain forms, so this works without JS. --}}
                            <div class="flex w-fit items-center rounded-lg border border-line">
                                <form method="POST" action="{{ route('site.cart.update') }}">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $line['id'] }}">
                                    <button type="submit" name="qty" value="{{ $line['qty'] - 1 }}"
                                            class="grid h-8 w-8 place-items-center text-muted transition-colors hover:bg-canvas"
                                            aria-label="Decrease quantity of {{ $line['name'] }}">
                                        <x-ui.icon name="minus" :size="14" />
                                    </button>
                                </form>

                                <span class="w-8 text-center text-sm font-semibold">{{ $line['qty'] }}</span>

                                <form method="POST" action="{{ route('site.cart.update') }}">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $line['id'] }}">
                                    <button type="submit" name="qty" value="{{ min(10, $line['qty'] + 1) }}"
                                            class="grid h-8 w-8 place-items-center text-muted transition-colors hover:bg-canvas
                                                   disabled:opacity-30"
                                            @disabled($line['qty'] >= 10)
                                            aria-label="Increase quantity of {{ $line['name'] }}">
                                        <x-ui.icon name="plus" :size="14" />
                                    </button>
                                </form>
                            </div>

                            <span class="text-sm font-extrabold sm:w-20 sm:text-right">
                                ₹{{ number_format($line['line_total']) }}
                            </span>

                            <form method="POST" action="{{ route('site.cart.remove') }}" class="justify-self-end">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $line['id'] }}">
                                <button type="submit"
                                        class="rounded p-1.5 text-muted transition-colors hover:bg-canvas hover:text-danger"
                                        aria-label="Remove {{ $line['name'] }} from cart">
                                    <x-ui.icon name="close" :size="15" />
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>

                {{-- Coupon --}}
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div>
                        <p class="mb-1.5 flex items-center gap-1.5 text-xs font-bold">
                            <x-ui.icon name="tag" :size="14" class="text-brand-500" /> Apply Coupon Code
                        </p>

                        @if ($totals['coupon_code'])
                            <div class="flex items-center justify-between gap-2 rounded-lg bg-brand-50 px-3 py-2.5">
                                <span class="flex items-center gap-1.5 text-xs font-bold text-brand-600">
                                    <x-ui.icon name="check-circle" :size="14" />
                                    {{ $totals['coupon_code'] }} applied
                                </span>
                                <form method="POST" action="{{ route('site.cart.coupon.remove') }}">
                                    @csrf
                                    <button type="submit"
                                            class="text-[11px] font-semibold text-muted underline underline-offset-2">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @else
                            <form method="POST" action="{{ route('site.cart.coupon') }}" class="flex gap-2">
                                @csrf
                                <input type="text" name="code" placeholder="Enter coupon code"
                                       aria-label="Coupon code" class="ab-field" required maxlength="50">
                                <button type="submit" class="ab-btn ab-btn-primary px-4 text-xs">Apply</button>
                            </form>
                        @endif
                    </div>

                    <div class="flex items-center gap-2.5 rounded-lg bg-brand-50 px-3 py-2.5">
                        <x-ui.icon name="tag" :size="18" class="text-brand-500" />
                        <span class="text-xs leading-tight">
                            <span class="font-bold">Get 5% Off</span> on your first order!<br>
                            Use code: <span class="font-bold">AUTO5</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================================ address + delivery --}}
        <div class="space-y-5 lg:col-span-4" x-data="{ addingAddress: {{ $addresses->isEmpty() ? 'true' : 'false' }} }">

            {{-- Address --}}
            <div class="ab-card p-5">
                <h2 class="mb-3 flex items-center gap-2 text-base font-extrabold">
                    <x-ui.icon name="pin" :size="18" class="text-brand-500" /> Delivery Address
                </h2>

                @if ($addresses->isEmpty())
                    <p class="mb-3 rounded-lg bg-accent-50 px-3 py-2 text-xs text-ink-soft">
                        Add a delivery address to continue.
                    </p>
                @else
                    <ul class="space-y-2.5">
                        @foreach ($addresses as $address)
                            <li>
                                <label class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition-colors
                                              has-checked:border-brand-500 has-checked:bg-brand-50 border-line hover:border-brand-300">
                                    <input type="radio" name="address_id" value="{{ $address->id }}"
                                           form="place-order-form"
                                           @checked($loop->first)
                                           class="mt-1 h-4 w-4 border-line text-brand-500 focus:ring-brand-400">

                                    <span class="flex-1 leading-snug">
                                        <span class="flex items-center gap-2">
                                            <span class="text-sm font-bold">{{ $address->label }}</span>
                                            @if ($address->is_default)
                                                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-bold text-brand-600">
                                                    Default
                                                </span>
                                            @endif
                                        </span>
                                        <span class="mt-0.5 block text-xs">{{ $address->name }}</span>
                                        @foreach ($address->lines as $l)
                                            <span class="block text-xs text-muted">{{ $l }}</span>
                                        @endforeach
                                        <span class="block text-xs text-muted">{{ $address->mobile }}</span>
                                    </span>
                                </label>
                            </li>
                        @endforeach
                    </ul>

                    <button type="button" @click="addingAddress = ! addingAddress"
                            class="mt-2.5 flex w-full items-center gap-2.5 rounded-lg border border-dashed border-line p-3
                                   text-sm font-semibold text-brand-500 transition-colors hover:bg-canvas">
                        <x-ui.icon name="plus" :size="18" /> Add New Address
                    </button>
                @endif

                {{-- New address form --}}
                <form method="POST" action="{{ route('site.checkout.address') }}"
                      x-show="addingAddress" x-cloak class="mt-4 space-y-3 border-t border-line pt-4">
                    @csrf

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label for="ad-label" class="ab-label">Label</label>
                            <select id="ad-label" name="label" class="ab-field">
                                <option>Home</option><option>Office</option><option>Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="ad-name" class="ab-label">Full Name <span class="text-danger">*</span></label>
                            <input id="ad-name" name="name" type="text" required class="ab-field"
                                   value="{{ old('name', auth()->user()->name ?? '') }}">
                        </div>
                        <div>
                            <label for="ad-mobile" class="ab-label">Mobile <span class="text-danger">*</span></label>
                            <input id="ad-mobile" name="mobile" type="tel" required class="ab-field"
                                   value="{{ old('mobile', auth()->user()->phone_number ?? '') }}">
                        </div>
                        <div>
                            <label for="ad-pincode" class="ab-label">Pincode</label>
                            <input id="ad-pincode" name="pincode" type="text" maxlength="10" class="ab-field">
                        </div>
                    </div>

                    <div>
                        <label for="ad-l1" class="ab-label">Address Line 1 <span class="text-danger">*</span></label>
                        <input id="ad-l1" name="address_line_1" type="text" required class="ab-field">
                    </div>
                    <div>
                        <label for="ad-l2" class="ab-label">Address Line 2</label>
                        <input id="ad-l2" name="address_line_2" type="text" class="ab-field">
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        <div>
                            <label for="ad-city" class="ab-label">City</label>
                            <input id="ad-city" name="city" type="text" class="ab-field">
                        </div>
                        <div>
                            <label for="ad-district" class="ab-label">District</label>
                            <input id="ad-district" name="district" type="text" class="ab-field">
                        </div>
                        <div>
                            <label for="ad-state" class="ab-label">State</label>
                            <input id="ad-state" name="state" type="text" class="ab-field"
                                   value="{{ $locations['default']['state'] }}">
                        </div>
                    </div>

                    <button type="submit" class="ab-btn ab-btn-primary w-full text-xs">Save Address</button>
                </form>
            </div>

            {{-- Delivery. Changing it recalculates shipping server-side. --}}
            <div class="ab-card p-5">
                <h2 class="mb-3 flex items-center gap-2 text-base font-extrabold">
                    <x-ui.icon name="truck" :size="18" class="text-brand-500" /> Delivery Options
                </h2>

                <p class="mb-3 flex items-center gap-2 rounded-lg bg-violet-50 px-3 py-2 text-[11px]">
                    <x-ui.icon name="truck" :size="15" class="text-violet-600" />
                    <span><span class="font-bold">Shiprocket</span> — Fast, Reliable Delivery Across India</span>
                </p>

                <form method="GET" action="{{ route('site.checkout') }}" x-data>
                    <ul class="space-y-2.5">
                        @foreach ($deliveryOptions as $key => $option)
                            @php
                                $isFree = $option['free_above'] && $totals['subtotal'] >= $freeShippingAbove;
                            @endphp
                            <li>
                                <label class="flex cursor-pointer items-center gap-3 rounded-lg border p-3 transition-colors
                                              {{ $delivery === $key ? 'border-brand-500 bg-brand-50' : 'border-line hover:border-brand-300' }}">
                                    <input type="radio" name="delivery_option" value="{{ $key }}"
                                           @checked($delivery === $key)
                                           @change="$el.form.submit()"
                                           class="h-4 w-4 border-line text-brand-500 focus:ring-brand-400">

                                    <span class="flex-1 leading-tight">
                                        <span class="block text-sm font-bold">{{ $option['label'] }}</span>
                                        <span class="block text-xs text-muted">{{ $option['note'] }}</span>
                                    </span>

                                    <span class="text-right leading-tight">
                                        @if ($isFree || $option['price'] == 0)
                                            <span class="block text-sm font-bold text-success">Free</span>
                                        @else
                                            <span class="block text-sm font-bold">₹{{ $option['price'] }}</span>
                                        @endif
                                        @if ($option['free_above'])
                                            <span class="block text-[10px] text-muted">
                                                (on orders above ₹{{ number_format($freeShippingAbove) }})
                                            </span>
                                        @endif
                                    </span>
                                </label>
                            </li>
                        @endforeach
                    </ul>

                    <noscript>
                        <button type="submit" class="ab-btn ab-btn-ghost mt-3 w-full text-xs">Update Delivery</button>
                    </noscript>
                </form>
            </div>
        </div>

        {{-- ================================================ summary + payment --}}
        <div class="space-y-5 lg:col-span-3">

            <div class="ab-card p-5">
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
                        <dt class="text-muted">Shipping Charges</dt>
                        <dd class="font-semibold">
                            {{ $totals['shipping'] > 0 ? '₹' . number_format($totals['shipping']) : '₹0' }}
                        </dd>
                    </div>
                </dl>

                <div class="mt-3 flex items-end justify-between border-t border-line pt-3">
                    <span class="leading-tight">
                        <span class="block text-sm font-extrabold">Total Amount</span>
                        <span class="block text-[10px] text-muted">(Inclusive of all taxes)</span>
                    </span>
                    <span class="text-xl font-extrabold">₹{{ number_format($totals['total']) }}</span>
                </div>

                @if ($totals['discount'] > 0)
                    <p class="mt-3 flex items-center gap-2 rounded-lg bg-brand-50 px-3 py-2 text-xs font-semibold text-brand-600">
                        <x-ui.icon name="tag" :size="14" />
                        You are saving ₹{{ number_format($totals['discount']) }} on this order!
                    </p>
                @endif
            </div>

            {{-- Payment + place order --}}
            <form method="POST" action="{{ route('site.checkout.place') }}" id="place-order-form"
                  class="ab-card p-5">
                @csrf
                <input type="hidden" name="delivery_option" value="{{ $delivery }}">

                <h2 class="mb-3 flex items-center gap-2 text-base font-extrabold">
                    <span class="grid h-6 w-6 place-items-center rounded-full bg-brand-500 text-xs font-bold text-white">3</span>
                    Payment Method
                </h2>

                <ul class="space-y-2">
                    @foreach ($paymentMethods as $method)
                        <li>
                            <label class="flex cursor-pointer items-center gap-3 rounded-lg border border-line p-2.5
                                          transition-colors hover:border-brand-300
                                          has-checked:border-brand-500 has-checked:bg-brand-50">
                                <input type="radio" name="payment_mode" value="{{ $method['id'] }}"
                                       @checked($loop->first) required
                                       class="h-4 w-4 border-line text-brand-500 focus:ring-brand-400">

                                <span class="flex-1 leading-tight">
                                    <span class="block text-xs font-bold">{{ $method['label'] }}</span>
                                    @if ($method['note'])
                                        <span class="block text-[10px] text-muted">{{ $method['note'] }}</span>
                                    @endif
                                </span>

                                <span class="flex gap-1">
                                    @foreach (array_slice($method['brands'], 0, 3) as $brandName)
                                        <span class="rounded border border-line px-1.5 py-0.5 text-[8px] font-bold text-muted">
                                            {{ $brandName }}
                                        </span>
                                    @endforeach
                                </span>
                            </label>
                        </li>
                    @endforeach
                </ul>

                <button type="submit" class="ab-btn ab-btn-primary mt-4 w-full py-3 disabled:opacity-50"
                        @disabled($addresses->isEmpty())>
                    <x-ui.icon name="lock" :size="16" />
                    Place Order ₹{{ number_format($totals['total']) }}
                    <x-ui.icon name="arrow-right" :size="16" />
                </button>

                @if ($addresses->isEmpty())
                    <p class="mt-2 text-center text-[11px] text-danger">Add a delivery address first.</p>
                @endif

                <p class="mt-2 text-center text-[10px] leading-relaxed text-muted">
                    By placing the order, you agree to our
                    <a href="{{ route('site.page', 'terms-conditions') }}" class="text-brand-500 underline">Terms &amp; Conditions</a>
                    and
                    <a href="{{ route('site.page', 'privacy-policy') }}" class="text-brand-500 underline">Privacy Policy</a>.
                </p>
            </form>
        </div>
    </div>
    @endif
</section>

<x-ui.shop-promises :promises="$site['shop_promises']" />

@endsection
