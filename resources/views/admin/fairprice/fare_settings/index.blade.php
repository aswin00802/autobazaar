@extends('admin.layouts.app')
@section('title')
FairPrice Fare Settings
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <h5 class="card-header">FairPrice Fare Settings</h5>
            <div class="card-body">
                <form action="{{ route('fairprice.fare-settings.update') }}" method="post" class="row g-4">
                    @csrf
                    <div class="col-12"><h6 class="mb-0">City Ride — Passenger Fare Slabs</h6></div>
                    <div class="col-md-4">
                        <label class="form-label">Base KM (shared)</label>
                        <input type="number" step="0.01" name="base_km" class="form-control" value="{{ old('base_km', $settings->base_km ?? 1.8) }}" required>
                        <small class="text-muted">Flat base fare applies up to this KM, then per-km rate.</small>
                    </div>

                    <div class="col-12"><strong>1 Passenger</strong></div>
                    <div class="col-md-4">
                        <label class="form-label">1 Pax Base Fare (₹)</label>
                        <input type="number" step="0.01" name="passenger_1_base_fare" class="form-control" value="{{ old('passenger_1_base_fare', $settings->passenger_1_base_fare ?? 50) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">1 Pax Extra KM Rate (₹)</label>
                        <input type="number" step="0.01" name="passenger_1_per_km" class="form-control" value="{{ old('passenger_1_per_km', $settings->passenger_1_per_km ?? 10) }}" required>
                    </div>

                    <div class="col-12"><strong>2 Passengers</strong></div>
                    <div class="col-md-4">
                        <label class="form-label">2 Pax Base Fare (₹)</label>
                        <input type="number" step="0.01" name="passenger_2_base_fare" class="form-control" value="{{ old('passenger_2_base_fare', $settings->passenger_2_base_fare ?? 60) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">2 Pax Extra KM Rate (₹)</label>
                        <input type="number" step="0.01" name="passenger_2_per_km" class="form-control" value="{{ old('passenger_2_per_km', $settings->passenger_2_per_km ?? 20) }}" required>
                    </div>

                    <div class="col-12"><strong>3 Passengers</strong></div>
                    <div class="col-md-4">
                        <label class="form-label">3 Pax Base Fare (₹)</label>
                        <input type="number" step="0.01" name="passenger_3_base_fare" class="form-control" value="{{ old('passenger_3_base_fare', $settings->passenger_3_base_fare ?? 70) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">3 Pax Extra KM Rate (₹)</label>
                        <input type="number" step="0.01" name="passenger_3_per_km" class="form-control" value="{{ old('passenger_3_per_km', $settings->passenger_3_per_km ?? 30) }}" required>
                    </div>

                    <div class="col-12 mt-2"><h6 class="mb-0">Pickup / Waiting</h6></div>
                    <div class="col-md-4">
                        <label class="form-label">Pickup Rate (₹/km)</label>
                        <input type="number" step="0.01" name="pickup_per_km_rate" class="form-control" value="{{ old('pickup_per_km_rate', $settings->pickup_per_km_rate ?? 18) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Free Pickup KM</label>
                        <input type="number" step="0.01" name="free_pickup_km" class="form-control" value="{{ old('free_pickup_km', $settings->free_pickup_km ?? 2) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Waiting Free Minutes</label>
                        <input type="number" name="waiting_free_mins" class="form-control" value="{{ old('waiting_free_mins', $settings->waiting_free_mins ?? 5) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Waiting Rate (₹/min)</label>
                        <input type="number" step="0.01" name="waiting_per_min_rate" class="form-control" value="{{ old('waiting_per_min_rate', $settings->waiting_per_min_rate ?? 2) }}" required>
                    </div>

                    {{-- Kept for backward compatibility with older rows / hire fallback --}}
                    <input type="hidden" name="base_fare" value="{{ old('base_fare', $settings->base_fare ?? 50) }}">
                    <input type="hidden" name="trip_per_km_rate" value="{{ old('trip_per_km_rate', $settings->trip_per_km_rate ?? 18) }}">

                    <div class="col-12 mt-2"><h6 class="mb-0">Tour / Hire (Round Trip Approx)</h6></div>
                    <div class="col-md-4">
                        <label class="form-label">Tour Rate (₹/km)</label>
                        <input type="number" step="0.01" name="tour_per_km_rate" class="form-control" value="{{ old('tour_per_km_rate', $settings->tour_per_km_rate ?? 18) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tour Min One-Way KM</label>
                        <input type="number" step="0.01" name="tour_min_km" class="form-control" value="{{ old('tour_min_km', $settings->tour_min_km ?? 200) }}" required>
                        <small class="text-muted">Round trip = billable one-way × 2</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hire Daily Rate (₹/km)</label>
                        <input type="number" step="0.01" name="hire_daily_per_km_rate" class="form-control" value="{{ old('hire_daily_per_km_rate', $settings->hire_daily_per_km_rate ?? 18) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hire Monthly Rate (₹/km)</label>
                        <input type="number" step="0.01" name="hire_monthly_per_km_rate" class="form-control" value="{{ old('hire_monthly_per_km_rate', $settings->hire_monthly_per_km_rate ?? 15) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Hire/Tour Advance % (optional online later)</label>
                        <input type="number" name="hire_tour_advance_percent" class="form-control" value="{{ old('hire_tour_advance_percent', $settings->hire_tour_advance_percent ?? 50) }}" required>
                        <small class="text-muted">Bookings can pay cash at end; online advance kept for later.</small>
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
