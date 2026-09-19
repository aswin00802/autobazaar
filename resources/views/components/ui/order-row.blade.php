@props(['order'])

{{-- One accessory order in the account area (App\Models\Shop\Order). --}}

@php
    $first = $order->items->first();
    $more = max(0, $order->items->count() - 1);
    $tones = [
        'placed' => 'bg-accent-100 text-ink',
        'confirmed' => 'bg-blue-50 text-info',
        'packed' => 'bg-blue-50 text-info',
        'shipped' => 'bg-brand-50 text-brand-600',
        'delivered' => 'bg-brand-500 text-white',
        'cancelled' => 'bg-red-50 text-danger',
    ];
@endphp

<a href="{{ route('site.account.order', $order->order_number) }}"
   class="ab-card flex flex-wrap items-center gap-4 p-4 ab-lift sm:gap-5 sm:p-5">

    <span class="grid h-14 w-14 shrink-0 place-items-center rounded-lg bg-canvas text-brand-500">
        <x-ui.icon name="box" :size="24" />
    </span>

    <span class="min-w-0 flex-1">
        <span class="block text-xs text-muted">
            Order {{ $order->order_number }} · {{ $order->created_at?->format('d M Y') }}
        </span>
        <span class="block truncate text-sm font-extrabold">
            {{ $first?->product_name ?? 'Accessory order' }}
            @if ($more) <span class="font-semibold text-muted">+ {{ $more }} more</span> @endif
        </span>
        <span class="mt-1.5 inline-block rounded-full px-2.5 py-1 text-[10px] font-bold {{ $tones[$order->order_status] ?? 'bg-canvas text-muted' }}">
            {{ ucfirst($order->order_status) }}
        </span>
    </span>

    <span class="ml-auto text-right">
        <span class="block text-[11px] text-muted">{{ $order->payment_status === 'paid' ? 'Paid' : 'Order Total' }}</span>
        <span class="block text-sm font-bold">₹{{ number_format($order->total_amount) }}</span>
        <span class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-brand-500">
            Track Order <x-ui.icon name="arrow-right" :size="13" />
        </span>
    </span>
</a>
