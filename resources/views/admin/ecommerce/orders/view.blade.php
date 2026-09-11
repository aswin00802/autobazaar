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
@endphp

@if(session('success'))
    <div class="alert alert-success alert-dismissible">{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible">{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row">

    {{-- ================================================================ items --}}
    <div class="col-12 col-lg-8">
        <div class="card mb-4">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>Order {{ $order->order_number }}</span>
                <span class="badge {{ $badge[$order->order_status] ?? 'bg-label-secondary' }}">
                    {{ ucfirst($order->order_status) }}
                </span>
            </h5>

            <div class="card-body">
                <p class="text-muted mb-3">
                    Placed on {{ $order->created_at->format('d M Y, g:i A') }}
                    @if($order->user) &middot; {{ $order->user->name }} @endif
                </p>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th><th>Fits</th><th>Qty</th><th>Unit Price</th><th>Line Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->brand_name ?? '—' }}</td>
                                    <td>{{ $item->qty }}</td>
                                    <td>₹{{ number_format($item->unit_price) }}</td>
                                    <td>₹{{ number_format($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end">Subtotal</td>
                                <td>₹{{ number_format($order->subtotal) }}</td>
                            </tr>
                            @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="4" class="text-end text-success">
                                        Discount ({{ $order->coupon_code }})
                                    </td>
                                    <td class="text-success">− ₹{{ number_format($order->discount_amount) }}</td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="4" class="text-end">
                                    Shipping ({{ ucfirst($order->delivery_option) }})
                                </td>
                                <td>{{ $order->shipping_amount > 0 ? '₹' . number_format($order->shipping_amount) : 'Free' }}</td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Total</td>
                                <td>₹{{ number_format($order->total_amount) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Status history --}}
        <div class="card mb-4">
            <h5 class="card-header">Status History</h5>
            <div class="card-body">
                @forelse ($order->history->sortByDesc('created_at') as $h)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <span class="badge {{ $badge[$h->order_status] ?? 'bg-label-secondary' }}">
                                {{ ucfirst($h->order_status) }}
                            </span>
                            @if($h->note)<span class="ms-2 text-muted">{{ $h->note }}</span>@endif
                        </div>
                        <small class="text-muted">{{ $h->created_at->format('d M Y, g:i A') }}</small>
                    </div>
                @empty
                    <p class="text-muted mb-0">No history recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- =============================================================== actions --}}
    <div class="col-12 col-lg-4">

        <div class="card mb-4">
            <h5 class="card-header">Update Status</h5>
            <div class="card-body">
                <form method="POST" action="{{ route('ecommerce.orders.status-update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $order->id }}">

                    <div class="mb-3">
                        <label class="form-label">Order Status</label>
                        <select name="order_status" class="form-select" required>
                            @foreach (array_merge($flow, ['cancelled']) as $s)
                                <option value="{{ $s }}" @selected($order->order_status === $s)>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Note (optional)</label>
                        <input type="text" name="note" class="form-control" maxlength="255"
                               placeholder="Handed to courier, etc.">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>

                <hr>

                <form method="POST" action="{{ route('ecommerce.orders.payment-update') }}">
                    @csrf
                    <input type="hidden" name="id" value="{{ $order->id }}">

                    <div class="mb-3">
                        <label class="form-label">Payment Status</label>
                        <select name="payment_status" class="form-select" required>
                            @foreach (['pending', 'cod_pending', 'paid', 'failed', 'refunded'] as $s)
                                <option value="{{ $s }}" @selected($order->payment_status === $s)>
                                    {{ ucfirst(str_replace('_', ' ', $s)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-outline-primary w-100">Update Payment</button>
                </form>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Delivery Address</h5>
            <div class="card-body">
                <p class="mb-1 fw-bold">{{ $order->shipping_name }}</p>
                <p class="mb-1">{{ $order->shipping_mobile }}</p>
                <p class="mb-0 text-muted">
                    {{ $order->shipping_address_line_1 }}<br>
                    @if($order->shipping_address_line_2){{ $order->shipping_address_line_2 }}<br>@endif
                    {{ collect([$order->shipping_city, $order->shipping_pincode])->filter()->implode(' - ') }}<br>
                    {{ $order->shipping_district }}, {{ $order->shipping_state }}
                </p>
            </div>
        </div>

        <div class="card mb-4">
            <h5 class="card-header">Payment</h5>
            <div class="card-body">
                <p class="mb-1">Method: <strong>{{ strtoupper($order->payment_mode) }}</strong></p>
                <p class="mb-1">Status: <strong>{{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}</strong></p>
                @if($order->paid_at)
                    <p class="mb-0">Paid at: {{ $order->paid_at->format('d M Y, g:i A') }}</p>
                @endif
            </div>
        </div>

        <a href="{{ route('ecommerce.orders') }}" class="btn btn-outline-secondary w-100">Back to Orders</a>
    </div>
</div>
@endsection
