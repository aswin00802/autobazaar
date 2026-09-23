@extends('admin.layouts.app')
@section('title')
FairPrice Ride Requests
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
@endpush

@section('content')
@php
    $pageTitle = $isFiltered
        ? ($historyType === 'customer' ? 'Customer Ride History' : ($historyType === 'driver' ? 'Driver Ride History' : 'Filtered Rides'))
        : 'All Ride Requests';
@endphp

<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h5 class="mb-1">FairPrice - {{ $pageTitle }}</h5>
                    @if($filterLabel)
                        <small class="text-muted">{{ $filterLabel }}</small>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-2">
                    @if($isFiltered)
                        <a href="{{ route('fairprice.rides') }}" class="btn btn-sm btn-outline-secondary">All Ride Requests</a>
                    @endif
                    <small class="text-muted">Total: {{ $rides->count() }}</small>
                </div>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>
                            <th>Customer</th>
                            <th>Driver</th>
                            <th>Booking</th>
                            <th>Ride For</th>
                            <th>Pickup</th>
                            <th>Drop</th>
                            <th>Scheduled</th>
                            <th>Fare</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($rides))
                        @foreach($rides as $ride)
                            @php
                                $displayFare = (float) ($ride->fare ?: $ride->estimated_fare ?: 0);
                                $isInstant = ($ride->booking_type ?? 'instant') === 'instant';
                                $statusClass = match ($ride->status) {
                                    'scheduled' => 'text-bg-warning',
                                    'pending' => 'text-bg-info',
                                    'accepted' => 'text-bg-primary',
                                    'arrived' => 'text-bg-secondary',
                                    'started' => 'text-bg-success',
                                    'completed' => 'text-bg-dark',
                                    'cancelled', 'rejected' => 'text-bg-danger',
                                    default => 'text-bg-secondary',
                                };
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($ride->customer_id)
                                        <a href="{{ route('fairprice.rides', ['customer_id' => $ride->customer_id, 'history_type' => 'customer']) }}">
                                            {{ $ride->customer->name ?? 'N/A' }}
                                        </a>
                                    @else
                                        {{ $ride->customer->name ?? 'N/A' }}
                                    @endif
                                    <br>
                                    <small>{{ $ride->customer->phone ?? '-' }}</small>
                                    @if(!empty($ride->other_phone))
                                        <br><small class="text-muted">Other: {{ $ride->other_phone }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($ride->driver_id)
                                        <a href="{{ route('fairprice.rides', ['driver_id' => $ride->driver_id, 'history_type' => 'driver']) }}">
                                            {{ $ride->driver->name ?? '-' }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ ucfirst($ride->booking_type ?? 'instant') }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $ride->ride_for ?? 'self')) }}</td>
                                <td>{{ $ride->pickup }}</td>
                                <td>{{ $ride->drop }}</td>
                                <td>{{ $ride->scheduled_at ? $ride->scheduled_at->format('d-m-Y h:i A') : '-' }}</td>
                                <td>₹ {{ number_format($displayFare, 2) }}</td>
                                <td>
                                    @if($isInstant)
                                        @if($ride->collected_by === 'hand')
                                            Collected by hand
                                        @elseif($ride->collected_by === 'online' && $ride->payment_status === 'paid')
                                            Paid online
                                        @else
                                            Pending collection
                                        @endif
                                    @else
                                        {{ ucwords(str_replace('_', ' ', $ride->payment_status ?? 'unpaid')) }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill {{ $statusClass }}">{{ ucfirst($ride->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('fairprice.rides.show', $ride->id) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon" title="View">
                                        <i class="icon-base ri ri-eye-line icon-20px"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
@endpush
