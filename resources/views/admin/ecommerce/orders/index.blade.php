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

        <div class="card">
            <h5 class="card-header">Website Orders</h5>

            <div class="card-body">

                {{-- Status tabs --}}
                <ul class="nav nav-pills flex-wrap mb-4">
                    @foreach ($tabs as $key => $label)
                        <li class="nav-item">
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

                {{-- Search --}}
                <form method="GET" class="row g-2 mb-4">
                    <div class="col-md-4">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                               placeholder="Order number, name or mobile">
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(request('q'))
                            <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Clear</a>
                        @endif
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl.no</th>
                                <th>Order No</th>
                                <th>Customer</th>
                                <th>Mobile</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th>Placed On</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration + ($orders->firstItem() - 1) }}</td>
                                    <td><span class="fw-bold">{{ $order->order_number }}</span></td>
                                    <td>{{ $order->shipping_name }}</td>
                                    <td>{{ $order->shipping_mobile }}</td>
                                    <td>{{ $order->items->sum('qty') }}</td>
                                    <td>₹{{ number_format($order->total_amount) }}</td>
                                    <td>
                                        <span class="badge {{ $order->payment_status === 'paid' ? 'bg-label-success' : 'bg-label-warning' }}">
                                            {{ strtoupper($order->payment_mode) }} / {{ str_replace('_', ' ', $order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $badge[$order->order_status] ?? 'bg-label-secondary' }}">
                                            {{ ucfirst($order->order_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('d M Y, g:i A') }}</td>
                                    <td>
                                        <a href="{{ route('ecommerce.orders.view', $order->id) }}"
                                           class="btn btn-sm btn-info">
                                            <i class="icon-base ri ri-eye-line icon-16px"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
