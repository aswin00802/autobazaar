@extends('admin.layouts.app')
@section('title')
E-commerce / Orders
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')

@php
    $tabs = ['all' => 'All'] + collect($flow)->mapWithKeys(fn ($s) => [$s => ucfirst($s)])->all()
            + ['cancelled' => 'Cancelled'];

    $badge = [
        'placed' => 'bg-label-secondary', 'confirmed' => 'bg-label-info',
        'packed' => 'bg-label-warning',   'shipped' => 'bg-label-primary',
        'delivered' => 'bg-label-success','cancelled' => 'bg-label-danger',
    ];

    // Small summary across the top. Everything here comes from $counts, which the
    // screen already had for the tabs — no extra queries.
    $waiting = ($counts['placed'] ?? 0) + ($counts['confirmed'] ?? 0) + ($counts['packed'] ?? 0);

    $tiles = [
        ['All Orders',  $counts['all'] ?? 0,       'ri-shopping-bag-3-line', 'primary', 'all'],
        ['Needs Action', $waiting,                 'ri-time-line',           'warning', 'placed'],
        ['Shipped',     $counts['shipped'] ?? 0,   'ri-truck-line',          'info',    'shipped'],
        ['Delivered',   $counts['delivered'] ?? 0, 'ri-check-double-line',   'success', 'delivered'],
    ];
@endphp

<div class="row">
    <div class="col-12">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    {{-- Summary tiles. Each one is a link to that group of orders. --}}
    @foreach ($tiles as [$label, $value, $icon, $tone, $tab])
        <div class="col-sm-6 col-lg-3 mb-4">
            <a href="{{ $tab === 'all' ? route('ecommerce.orders') : route('ecommerce.orders.status', $tab) }}"
               class="card h-100 text-body text-decoration-none">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="badge bg-label-{{ $tone }} rounded p-2">
                        <i class="icon-base ri {{ $icon }} icon-24px"></i>
                    </span>
                    <span>
                        <span class="d-block text-muted small">{{ $label }}</span>
                        <span class="h4 mb-0 d-block">{{ number_format($value) }}</span>
                    </span>
                </div>
            </a>
        </div>
    @endforeach

    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <h5 class="mb-0">Website Orders</h5>

                {{-- Search --}}
                <form method="GET" class="d-flex flex-wrap gap-2 mb-0">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                           style="min-width: 260px;" placeholder="Order number, name or mobile">
                    <button type="submit" class="btn btn-primary">Search</button>
                    @if(request('q'))
                        <a href="{{ url()->current() }}" class="btn btn-label-secondary">Clear</a>
                    @endif
                </form>
            </div>

            {{-- Status tabs --}}
            <div class="card-body pb-0">
                <ul class="nav nav-pills flex-wrap mb-3">
                    @foreach ($tabs as $key => $label)
                        <li class="nav-item mb-1">
                            <a class="nav-link {{ $status === $key ? 'active' : '' }}"
                               href="{{ $key === 'all' ? route('ecommerce.orders') : route('ecommerce.orders.status', $key) }}">
                                {{ $label }}
                                <span class="badge {{ $status === $key ? 'bg-white text-primary' : 'bg-label-secondary' }} ms-1">
                                    {{ $counts[$key] ?? 0 }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order</th>
                            <th>Customer</th>
                            <th class="text-center">Items</th>
                            <th class="text-end">Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Placed</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration + ($orders->firstItem() - 1) }}</td>

                                {{-- The order number is the link too, not just the button --}}
                                <td>
                                    <a href="{{ route('ecommerce.orders.view', $order->id) }}" class="fw-semibold">
                                        {{ $order->order_number }}
                                    </a>
                                </td>

                                {{-- Name and a tap-to-call number in one column --}}
                                <td>
                                    <span class="d-block fw-medium">{{ $order->shipping_name }}</span>
                                    <a href="tel:{{ $order->shipping_mobile }}" class="small text-muted">
                                        {{ $order->shipping_mobile }}
                                    </a>
                                </td>

                                <td class="text-center">{{ $order->items->sum('qty') }}</td>
                                <td class="text-end fw-semibold">₹{{ number_format($order->total_amount) }}</td>

                                <td>
                                    <span class="d-block text-uppercase small fw-medium">{{ $order->payment_mode }}</span>
                                    <span class="badge {{ $order->payment_status === 'paid' ? 'bg-label-success' : 'bg-label-warning' }}">
                                        {{ str_replace('_', ' ', $order->payment_status) }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge {{ $badge[$order->order_status] ?? 'bg-label-secondary' }}">
                                        {{ ucfirst($order->order_status) }}
                                    </span>
                                </td>

                                {{-- Date on top, time underneath: easier to scan down the column --}}
                                <td>
                                    <span class="d-block">{{ $order->created_at->format('d M Y') }}</span>
                                    <span class="small text-muted">{{ $order->created_at->format('g:i A') }}</span>
                                </td>

                                <td class="text-end">
                                    <a href="{{ route('ecommerce.orders.view', $order->id) }}"
                                       class="btn btn-sm btn-label-primary">
                                        <i class="icon-base ri ri-eye-line icon-16px me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="icon-base ri ri-inbox-line icon-48px text-muted d-block mb-2"></i>
                                    <span class="d-block fw-medium">No orders here</span>
                                    <span class="text-muted small">
                                        @if (request('q'))
                                            Nothing matched “{{ request('q') }}”.
                                        @elseif ($status !== 'all')
                                            No orders are {{ $status }} at the moment.
                                        @else
                                            Orders placed on the website will appear here.
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($orders->hasPages() || $orders->total())
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2 pt-3">
                    <small class="text-muted">
                        @if ($orders->total())
                            Showing {{ number_format($orders->firstItem()) }} to {{ number_format($orders->lastItem()) }}
                            of {{ number_format($orders->total()) }} order{{ $orders->total() === 1 ? '' : 's' }}
                        @endif
                    </small>
                    <div>{{ $orders->onEachSide(1)->links() }}</div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
