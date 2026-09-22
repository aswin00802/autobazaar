@props(['product', 'compact' => false])

@php
    $discount = ($product['mrp'] ?? 0) > $product['price']
        ? (int) round((($product['mrp'] - $product['price']) / $product['mrp']) * 100)
        : null;

    // Live catalogue rows carry no rating — there is no review system yet, so
    // the block is omitted rather than filled with an invented number.
    $rating = $product['rating'] ?? null;
    $image = $product['image'] ?? null;
@endphp

<article {{ $attributes->merge(['class' => 'ab-card group flex flex-col overflow-hidden ab-lift']) }}>

    {{-- Image / placeholder + badges --}}
    <div class="relative bg-canvas p-4">
        @if ($product['badge'])
            <span class="absolute left-3 top-3 z-10 rounded px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white
                         {{ $product['badge'] === 'New' ? 'bg-success' : 'bg-danger' }}">
                {{ $product['badge'] }}
            </span>
        @endif

        @if ($discount)
            <span class="absolute right-3 top-3 z-10 rounded bg-accent-500 px-1.5 py-0.5 text-[10px] font-bold text-ink">
                {{ $discount }}% OFF
            </span>
        @endif

        @if ($image)
            <img src="{{ asset($image) }}" alt="{{ $product['name'] }}"
                 class="mx-auto h-28 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                 loading="lazy">
        @else
            <x-ui.product-art :art="$product['art']"
                              class="mx-auto h-28 w-auto transition-transform duration-300 group-hover:scale-105" />
        @endif
    </div>

    {{-- Body --}}
    <div class="flex flex-1 flex-col p-3.5">
        <h3 class="line-clamp-2 text-sm font-bold leading-snug">{{ $product['name'] }}</h3>

        @if ($product['subtitle'])
            <p class="mt-0.5 text-xs text-muted">{{ $product['subtitle'] }}</p>
        @endif

        <p class="mt-2 flex items-baseline gap-2">
            <span class="text-base font-extrabold">₹{{ number_format($product['price']) }}</span>
            @if ($discount)
                <span class="text-xs text-muted line-through">₹{{ number_format($product['mrp']) }}</span>
            @endif
        </p>

        @if ($rating)
            <div class="mt-1.5 flex items-center gap-1.5">
                <x-ui.rating :rating="$rating" :size="12" :show-value="false" />
                @if (! empty($product['reviews']))
                    <span class="text-[11px] text-muted">({{ $product['reviews'] }})</span>
                @endif
            </div>
        @elseif (! empty($product['brand']))
            {{-- Something useful in the rating's place, so cards stay level --}}
            <p class="mt-1.5 text-[11px] text-muted">Fits {{ $product['brand'] }}</p>
        @endif

        @unless ($compact)
            @if (auth()->check() && auth()->user()->isStaff())
                {{-- Staff see the shop as customers do, but cannot order from an admin account --}}
                <div class="mt-3.5">
                    <button type="button" disabled class="ab-btn ab-btn-primary w-full px-2 py-2 text-xs disabled:opacity-60">
                        <x-ui.icon name="cart" :size="15" />
                        <span>Add to Cart</span>
                    </button>
                    <p class="mt-1 text-center text-[10px] text-muted">Ordering is off for staff accounts</p>
                </div>
            @elseif (! empty($product['product_model_id']))
                <div x-data="addToCart({
                        productModelId: {{ $product['product_model_id'] }},
                        url: @js(route('site.cart.add')),
                        csrf: @js(csrf_token()),
                     })" class="mt-3.5">
                    <button type="button" @click="add()"
                            :disabled="state === 'saving'"
                            :class="state === 'added' ? 'bg-success hover:bg-success' : ''"
                            class="ab-btn ab-btn-primary w-full px-2 py-2 text-xs disabled:opacity-60">
                        <x-ui.icon name="cart" :size="15" x-show="state !== 'added'" />
                        <x-ui.icon name="check" :size="15" x-show="state === 'added'" x-cloak />
                        <span x-text="label">Add to Cart</span>
                    </button>
                    <p x-show="message" x-cloak x-text="message" class="mt-1 text-[10px] text-danger"></p>
                </div>
            @else
                {{-- No priced variant, so there is nothing to buy --}}
                <p class="mt-3.5 rounded-lg bg-canvas px-2 py-2 text-center text-xs text-muted">
                    Currently unavailable
                </p>
            @endif
        @endunless
    </div>
</article>
