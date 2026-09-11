{{-- Shared by create and edit. $coupon is null when creating. --}}
@php $coupon = $coupon ?? null; @endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="row g-3">

    <div class="col-md-4">
        <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
        <input type="text" name="code" class="form-control text-uppercase" required maxlength="50"
               value="{{ old('code', $coupon->code ?? '') }}" placeholder="AUTO5">
    </div>

    <div class="col-md-8">
        <label class="form-label">Description</label>
        <input type="text" name="label" class="form-control" maxlength="255"
               value="{{ old('label', $coupon->label ?? '') }}"
               placeholder="5% off on your first order">
        <small class="text-muted">Shown to the customer when the coupon is applied.</small>
    </div>

    <div class="col-md-4">
        <label class="form-label">Discount Type <span class="text-danger">*</span></label>
        <select name="discount_type" class="form-select" required>
            <option value="percent" @selected(old('discount_type', $coupon->discount_type ?? 'percent') === 'percent')>Percentage (%)</option>
            <option value="flat" @selected(old('discount_type', $coupon->discount_type ?? '') === 'flat')>Flat Amount (₹)</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Discount Value <span class="text-danger">*</span></label>
        <input type="number" name="discount_value" class="form-control" required min="0" step="0.01"
               value="{{ old('discount_value', $coupon->discount_value ?? '') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Maximum Discount (₹)</label>
        <input type="number" name="max_discount_amount" class="form-control" min="0" step="0.01"
               value="{{ old('max_discount_amount', $coupon->max_discount_amount ?? '') }}">
        <small class="text-muted">Caps a percentage discount. Leave blank for no cap.</small>
    </div>

    <div class="col-md-4">
        <label class="form-label">Minimum Order Amount (₹)</label>
        <input type="number" name="min_order_amount" class="form-control" min="0" step="0.01"
               value="{{ old('min_order_amount', $coupon->min_order_amount ?? 0) }}">
    </div>

    <div class="col-md-4">
        <label class="form-label">Total Usage Limit</label>
        <input type="number" name="usage_limit" class="form-control" min="1"
               value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}">
        <small class="text-muted">Blank means unlimited.</small>
    </div>

    <div class="col-md-4">
        <label class="form-label">Per Customer Limit</label>
        <input type="number" name="per_user_limit" class="form-control" min="1"
               value="{{ old('per_user_limit', $coupon->per_user_limit ?? 1) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Valid From</label>
        <input type="date" name="valid_from" class="form-control"
               value="{{ old('valid_from', $coupon?->valid_from?->format('Y-m-d') ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Valid To</label>
        <input type="date" name="valid_to" class="form-control"
               value="{{ old('valid_to', $coupon?->valid_to?->format('Y-m-d') ?? '') }}">
    </div>

    <div class="col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="first_order_only" value="1"
                   id="first_order_only" @checked(old('first_order_only', $coupon->first_order_only ?? 0))>
            <label class="form-check-label" for="first_order_only">
                First order only — customers who have ordered before cannot use it
            </label>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="status_id" value="1"
                   id="status_id" @checked(old('status_id', $coupon->status_id ?? 1))>
            <label class="form-check-label" for="status_id">Active</label>
        </div>
    </div>
</div>
