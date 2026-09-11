@extends('admin.layouts.app')
@section('title')
FairPrice Ride #{{ $ride->id }}
@endsection

@section('content')
@php
    $bookingType = $ride->booking_type ?? 'instant';
    $isPrebook = $bookingType === 'prebook';
    $isHireTour = in_array($bookingType, ['hire', 'tour'], true);
    $isAdvancePaid = in_array($bookingType, ['prebook', 'hire', 'tour'], true);
    $isInstant = !$isAdvancePaid;
    $finalFare = (float) ($ride->fare ?: $ride->estimated_fare ?: 0);
    $showOtherPhone = !empty($ride->other_phone);
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

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
    <div>
        <h4 class="mb-1">Ride #{{ $ride->id }}</h4>
        <span class="badge rounded-pill {{ $statusClass }}">{{ ucfirst($ride->status) }}</span>
        <span class="badge rounded-pill {{ $isAdvancePaid ? 'text-bg-warning' : 'text-bg-info' }}">{{ ucfirst($bookingType) }}</span>
        @if($bookingType === 'hire' && $ride->hire_type)
            <span class="badge rounded-pill text-bg-secondary">{{ ucfirst($ride->hire_type) }}</span>
        @endif
    </div>
    <a href="{{ route('fairprice.rides') }}" class="btn btn-outline-secondary">
        <i class="ri-arrow-left-line me-1"></i> All Ride Requests
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Booking Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Booking Type</label>
                        <p class="mb-0">{{ ucfirst($bookingType) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Ride For</label>
                        <p class="mb-0">{{ ucfirst(str_replace('_', ' ', $ride->ride_for ?? 'self')) }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Passengers</label>
                        <p class="mb-0">{{ $ride->passenger_count ?? 1 }}</p>
                    </div>
                    @if($showOtherPhone)
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Other Phone</label>
                        <p class="mb-0">{{ $ride->other_phone }}</p>
                    </div>
                    @endif
                    @if($bookingType === 'hire')
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Hire Type</label>
                        <p class="mb-0">{{ $ride->hire_type ? ucfirst($ride->hire_type) : '-' }}</p>
                    </div>
                    @endif
                    @if($isHireTour)
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Purpose</label>
                        <p class="mb-0">{{ $ride->purpose ? ucfirst($ride->purpose) : '-' }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Pickup At</label>
                        <p class="mb-0">{{ $ride->pickup_at ? $ride->pickup_at->format('d M Y, h:i A') : ($ride->scheduled_at ? $ride->scheduled_at->format('d M Y, h:i A') : '-') }}</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Drop At</label>
                        <p class="mb-0">{{ $ride->drop_at ? $ride->drop_at->format('d M Y, h:i A') : '-' }}</p>
                    </div>
                    @if(!empty($ride->booking_comment))
                    <div class="col-12">
                        <label class="form-label text-muted mb-1">Comment</label>
                        <p class="mb-0">{{ $ride->booking_comment }}</p>
                    </div>
                    @endif
                    @endif
                    @if($isPrebook)
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Scheduled At</label>
                        <p class="mb-0">{{ $ride->scheduled_at ? $ride->scheduled_at->format('d M Y, h:i A') : '-' }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Trip Route</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted mb-1">Pickup</label>
                        <p class="mb-0 fw-medium"><i class="ri-map-pin-line text-success me-1"></i>{{ $ride->pickup }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted mb-1">Drop</label>
                        <p class="mb-0 fw-medium"><i class="ri-flag-line text-danger me-1"></i>{{ $ride->drop ?: 'Not set (tour from-only)' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">People</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="mb-2">Customer</h6>
                            <p class="mb-1 fw-medium">
                                @if($ride->customer_id)
                                    <a href="{{ route('fairprice.rides', ['customer_id' => $ride->customer_id, 'history_type' => 'customer']) }}">
                                        {{ $ride->customer->name ?? 'N/A' }}
                                    </a>
                                    <small class="text-muted">({{ $customerHistoryCount }} rides)</small>
                                @else
                                    {{ $ride->customer->name ?? 'N/A' }}
                                @endif
                            </p>
                            <p class="mb-0 text-muted">{{ $ride->customer->phone ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100">
                            <h6 class="mb-2">Driver</h6>
                            <p class="mb-1 fw-medium">
                                @if($ride->driver_id)
                                    <a href="{{ route('fairprice.rides', ['driver_id' => $ride->driver_id, 'history_type' => 'driver']) }}">
                                        {{ $ride->driver->name ?? 'Not assigned' }}
                                    </a>
                                    <small class="text-muted">({{ $driverHistoryCount }} rides)</small>
                                @else
                                    Not assigned
                                @endif
                            </p>
                            <p class="mb-1 text-muted">{{ $ride->driver->phone_number ?? '-' }}</p>
                            @if(optional($ride->driver?->userInfo)->vehicle_no)
                                <p class="mb-0 text-muted">Vehicle: {{ $ride->driver->userInfo->vehicle_no }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Payment Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted mb-1">Collection Mode</label>
                        <p class="mb-0">
                            @if($isAdvancePaid)
                                {{ ucwords(str_replace('_', ' ', $ride->payment_status ?? 'unpaid')) }}
                            @elseif($ride->collected_by === 'hand')
                                Collected by hand
                            @elseif($ride->collected_by === 'online' && $ride->payment_status === 'paid')
                                Paid online
                            @else
                                Pending collection
                            @endif
                        </p>
                    </div>
                    @if($isAdvancePaid)
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Advance Amount</label>
                            <p class="mb-0">₹ {{ number_format((float) ($ride->advance_amount ?? 0), 2) }}</p>
                        </div>
                        @if(!empty($ride->razorpay_order_id))
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Razorpay Order</label>
                            <p class="mb-0 text-break">{{ $ride->razorpay_order_id }}</p>
                        </div>
                        @endif
                        @if(!empty($ride->razorpay_payment_id))
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Razorpay Payment</label>
                            <p class="mb-0 text-break">{{ $ride->razorpay_payment_id }}</p>
                        </div>
                        @endif
                    @elseif($ride->collected_by === 'online')
                        @if(!empty($ride->razorpay_order_id))
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Razorpay Order</label>
                            <p class="mb-0 text-break">{{ $ride->razorpay_order_id }}</p>
                        </div>
                        @endif
                        @if(!empty($ride->razorpay_payment_id))
                        <div class="col-md-4">
                            <label class="form-label text-muted mb-1">Razorpay Payment</label>
                            <p class="mb-0 text-break">{{ $ride->razorpay_payment_id }}</p>
                        </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Fare Summary</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Trip Fare</span>
                    <strong>₹ {{ number_format((float) ($ride->trip_fare ?? 0), 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Pickup Fare</span>
                    <strong>₹ {{ number_format((float) ($ride->pickup_fare ?? 0), 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Waiting Fare</span>
                    <strong>₹ {{ number_format((float) ($ride->waiting_fare ?? 0), 2) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between mb-1">
                    <span class="fw-medium">Estimated</span>
                    <strong>₹ {{ number_format((float) ($ride->estimated_fare ?? 0), 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="fw-medium text-primary">Final Fare</span>
                    <strong class="text-primary fs-5">₹ {{ number_format($finalFare, 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Distance</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Trip KM</span>
                    <strong>{{ $ride->trip_distance_km ?? '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Pickup KM</span>
                    <strong>{{ $ride->pickup_distance_km ?? '-' }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Waiting Mins</span>
                    <strong>{{ $ride->waiting_mins ?? 0 }}</strong>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Timeline</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><span class="text-muted">Requested:</span> {{ $ride->created_at?->format('d M Y, h:i A') }}</li>
                    @if($isAdvancePaid)
                        @if($ride->pickup_at)
                        <li class="mb-2"><span class="text-muted">Pickup:</span> {{ $ride->pickup_at->format('d M Y, h:i A') }}</li>
                        @elseif($ride->scheduled_at)
                        <li class="mb-2"><span class="text-muted">Scheduled:</span> {{ $ride->scheduled_at->format('d M Y, h:i A') }}</li>
                        @endif
                        @if($ride->drop_at)
                        <li class="mb-2"><span class="text-muted">Drop:</span> {{ $ride->drop_at->format('d M Y, h:i A') }}</li>
                        @endif
                        @if($ride->paid_at)
                        <li class="mb-2"><span class="text-muted">Payment Confirmed:</span> {{ $ride->paid_at->format('d M Y, h:i A') }}</li>
                        @endif
                        @if($ride->dispatched_at)
                        <li class="mb-2"><span class="text-muted">Driver Request Sent:</span> {{ $ride->dispatched_at->format('d M Y, h:i A') }}</li>
                        @endif
                    @endif
                    @if($ride->accepted_at)
                    <li class="mb-2"><span class="text-muted">Accepted:</span> {{ $ride->accepted_at->format('d M Y, h:i A') }}</li>
                    @endif
                    @if($ride->started_at)
                    <li class="mb-2"><span class="text-muted">Started:</span> {{ $ride->started_at->format('d M Y, h:i A') }}</li>
                    @endif
                    @if($ride->completed_at)
                    <li class="mb-0"><span class="text-muted">Completed:</span> {{ $ride->completed_at->format('d M Y, h:i A') }}</li>
                    @endif
                    @if($ride->cancelled_at)
                    <li class="mb-0"><span class="text-muted">Cancelled:</span> {{ $ride->cancelled_at->format('d M Y, h:i A') }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
