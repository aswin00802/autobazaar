@extends('admin.layouts.app')
@section('title')
E-commerce / Order {{ $order->order_number }}
@endsection

@section('content')

@php
    $badge = [
        'placed' => 'bg-label-secondary', 'confirmed' => 'bg-label-info',
        'packed' => 'bg-label-warning',   'shipped' => 'bg-label-primary',
        'delivered' => 'bg-label-success','cancelled' => 'bg-label-danger',
    ];

    $stepIcon = [
        'placed' => 'ri-shopping-bag-3-line', 'confirmed' => 'ri-check-line',
        'packed' => 'ri-archive-line',        'shipped' => 'ri-truck-line',
        'delivered' => 'ri-home-smile-line',
    ];

    $cancelled = $order->order_status === 'cancelled';
    $reached = array_search($order->order_status, $flow, true);      // false when cancelled

    /*
     * Only the statuses this order can actually move to. The rule lives in
     * OrderService::canMoveTo — forward only, cancel only before shipping, and
     * delivered or cancelled is the end. Offering the others just produced an
     * error message after the click.
     */
    $position = array_search($order->order_status, $flow, true);
    $nextSteps = $cancelled || $order->order_status === 'delivered'
        ? []
        : array_values(array_filter(
            array_merge($flow, ['cancelled']),
            fn ($s) => $s === 'cancelled'
                ? in_array($order->order_status, ['placed', 'confirmed', 'packed'], true)
                : ($position !== false && array_search($s, $flow, true) > $position)
        ));

    $paymentTone = ['paid' => 'success', 'refunded' => 'info', 'failed' => 'danger'][$order->payment_status] ?? 'warning';
@endphp

@if(session('success'))
    <div class="alert alert-success alert-dismissible">{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible">{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Header: where you are, and the way back --}}
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('ecommerce.orders') }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon" title="Back to orders">
            <i class="icon-base ri ri-arrow-left-line icon-20px"></i>
        </a>
        <div>
            <h5 class="mb-0">Order {{ $order->order_number }}</h5>
            <small class="text-muted">
                Placed {{ $order->created_at->format('d M Y') }} at {{ $order->created_at->format('g:i A') }}
            </small>
        </div>
    </div>

    <div class="d-flex align-items-center gap-2">
        <span class="badge {{ $badge[$order->order_status] ?? 'bg-label-secondary' }}">
            {{ ucfirst($order->order_status) }}
        </span>
        <span class="badge bg-label-{{ $paymentTone }}">
            {{ strtoupper($order->payment_mode) }} · {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
        </span>
    </div>
</div>

<div class="row">

    <div class="col-12 col-lg-8">

        {{-- How far along the order is, at a glance --}}
        <div class="card mb-4">
            <div class="card-body">
                @if ($cancelled)
                    <div class="d-flex align-items-center gap-2 text-danger">
                        <i class="icon-base ri ri-close-circle-line icon-24px"></i>
                        <span class="fw-medium">This order was cancelled.</span>
                    </div>
                @else
                    <div class="d-flex justify-content-between text-center">
                        @foreach ($flow as $i => $step)
                            @php $done = $reached !== false && $i <= $reached; @endphp
                            <div class="flex-fill position-relative">
                                @if ($i > 0)
                                    {{-- the joining line, coloured up to where the order has got to --}}
                                    <span class="position-absolute {{ $done ? 'bg-primary' : 'bg-label-secondary' }}"
                                          style="height:3px; left:-50%; right:50%; top:19px; z-index:0;"></span>
                                @endif
                                <span class="badge {{ $done ? 'bg-primary' : 'bg-label-secondary' }} rounded-circle p-2 position-relative"
                                      style="z-index:1;">
                                    <i class="icon-base ri {{ $stepIcon[$step] ?? 'ri-circle-line' }} icon-20px"></i>
                                </span>
                                <span class="d-block small mt-2 {{ $done ? 'fw-medium' : 'text-muted' }}">
                                    {{ ucfirst($step) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ============================================================= items --}}
        <div class="card mb-4">
            <h5 class="card-header">
                Items
                <span class="badge bg-label-secondary ms-1">{{ $order->items->sum('qty') }}</span>
            </h5>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Fits</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="fw-medium">{{ $item->product_name }}</td>
                                <td class="text-muted">{{ $item->brand_name ?? '—' }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-end">₹{{ number_format($item->unit_price) }}</td>
                                <td class="text-end fw-medium">₹{{ number_format($item->line_total) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals read better as a list than as more table rows --}}
            <div class="card-body border-top">
                <div class="row justify-content-end">
                    <div class="col-12 col-md-6 col-lg-5">
                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Subtotal</span>
                            <span>₹{{ number_format($order->subtotal) }}</span>
                        </div>

                        @if($order->discount_amount > 0)
                            <div class="d-flex justify-content-between py-1 text-success">
                                <span>Discount
                                    @if($order->coupon_code)
                                        <span class="badge bg-label-success ms-1">{{ $order->coupon_code }}</span>
                                    @endif
                                </span>
                                <span>− ₹{{ number_format($order->discount_amount) }}</span>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between py-1">
                            <span class="text-muted">Shipping ({{ ucfirst($order->delivery_option) }})</span>
                            <span>{{ $order->shipping_amount > 0 ? '₹' . number_format($order->shipping_amount) : 'Free' }}</span>
                        </div>

                        <hr class="my-2">

                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold h5 mb-0">₹{{ number_format($order->total_amount) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====================================================== status history --}}
        <div class="card mb-4">
            <h5 class="card-header">Status History</h5>
            <div class="card-body">
                @forelse ($order->history->sortByDesc('created_at') as $h)
                    <div class="d-flex gap-3 {{ ! $loop->last ? 'pb-3' : '' }}">
                        {{-- dot and the line down to the next entry --}}
                        <div class="d-flex flex-column align-items-center">
                            <span class="badge {{ $badge[$h->order_status] ?? 'bg-label-secondary' }} rounded-circle p-1"
                                  style="width:12px; height:12px;"></span>
                            @if (! $loop->last)
                                <span class="flex-grow-1 bg-label-secondary" style="width:2px;"></span>
                            @endif
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap justify-content-between gap-2">
                                <span class="fw-medium">{{ ucfirst($h->order_status) }}</span>
                                <small class="text-muted">{{ $h->created_at->format('d M Y, g:i A') }}</small>
                            </div>
                            @if($h->note)
                                <small class="text-muted d-block">{{ $h->note }}</small>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Nothing recorded yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- =============================================================== actions --}}
    <div class="col-12 col-lg-4">

        <div class="card mb-4">
            <h5 class="card-header">Move This Order On</h5>
            <div class="card-body">
                @if ($nextSteps)
                    <form method="POST" action="{{ route('ecommerce.orders.status-update') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $order->id }}">

                        <div class="mb-3">
                            <label class="form-label">Next Status</label>
                            <select name="order_status" class="form-select" required>
                                @foreach ($nextSteps as $s)
                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">An order only moves forward, and cannot be changed once delivered.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note (optional)</label>
                            <input type="text" name="note" class="form-control" maxlength="255"
                                   placeholder="Handed to courier, etc.">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                @else
                    <div class="text-center py-2">
                        <i class="icon-base ri {{ $cancelled ? 'ri-close-circle-line' : 'ri-check-double-line' }} icon-32px {{ $cancelled ? 'text-danger' : 'text-success' }} d-block mb-2"></i>
                        <span class="fw-medium d-block">
                            {{ $cancelled ? 'Cancelled' : 'Delivered' }}
                        </span>
                        <small class="text-muted">This order is finished and cannot be changed.</small>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Payment</h5>
            <div class="card-body">
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Method</span>
                    <span class="fw-medium text-uppercase">{{ $order->payment_mode }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Status</span>
                    <span class="badge bg-label-{{ $paymentTone }}">
                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                    </span>
                </div>
                @if($order->paid_at)
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Paid at</span>
                        <span>{{ $order->paid_at->format('d M Y, g:i A') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('ecommerce.orders.payment-update') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="id" value="{{ $order->id }}">
                    <div class="input-group">
                        <select name="payment_status" class="form-select" required>
                            @foreach (['pending', 'cod_pending', 'paid', 'failed', 'refunded'] as $s)
                                <option value="{{ $s }}" @selected($order->payment_status === $s)>
                                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Customer</h5>
            <div class="card-body">
                <p class="mb-1 fw-medium">{{ $order->shipping_name }}</p>

                {{-- Tap to call, or open WhatsApp: both come up a lot when chasing a delivery --}}
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <a href="tel:{{ $order->shipping_mobile }}" class="btn btn-sm btn-label-primary">
                        <i class="icon-base ri ri-phone-line icon-16px me-1"></i>{{ $order->shipping_mobile }}
                    </a>
                    <a href="https://wa.me/91{{ preg_replace('/\D+/', '', $order->shipping_mobile) }}"
                       target="_blank" rel="noopener" class="btn btn-sm btn-label-success">
                        <i class="icon-base ri ri-whatsapp-line icon-16px"></i>
                    </a>
                </div>

                <span class="d-block text-muted small mb-1">Delivery address</span>
                <address class="mb-0 text-muted">
                    {{ $order->shipping_address_line_1 }}<br>
                    @if($order->shipping_address_line_2){{ $order->shipping_address_line_2 }}<br>@endif
                    {{ collect([$order->shipping_city, $order->shipping_pincode])->filter()->implode(' - ') }}<br>
                    {{ collect([$order->shipping_district, $order->shipping_state])->filter()->implode(', ') }}
                </address>
            </div>
        </div>
    </div>
</div>
@endsection
