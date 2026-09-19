{{-- Cart lines + coupon box. Shared by the public cart page and checkout; needs $totals. --}}
@php $lines = $totals['lines']; @endphp

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
