@extends('admin.layouts.app')
@section('title')
E-commerce / Coupons
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible">{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible">{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="card">
            <h5 class="card-header d-flex justify-content-between align-items-center">
                <span>Coupons</span>
                <a href="{{ route('ecommerce.coupons.create') }}" class="btn btn-sm btn-primary">
                    <i class="icon-base ri ri-add-line icon-16px me-1"></i> Add Coupon
                </a>
            </h5>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Sl.no</th><th>Code</th><th>Description</th><th>Discount</th>
                                <th>Min Order</th><th>Used</th><th>Valid</th><th>Status</th><th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($coupons as $coupon)
                                <tr>
                                    <td>{{ $loop->iteration + ($coupons->firstItem() - 1) }}</td>
                                    <td><span class="badge bg-label-primary">{{ $coupon->code }}</span></td>
                                    <td>{{ $coupon->label ?? '—' }}</td>
                                    <td>
                                        {{ $coupon->discount_type === 'percent'
                                            ? $coupon->discount_value . '%'
                                            : '₹' . number_format($coupon->discount_value) }}
                                        @if($coupon->max_discount_amount)
                                            <small class="text-muted">(max ₹{{ number_format($coupon->max_discount_amount) }})</small>
                                        @endif
                                    </td>
                                    <td>{{ $coupon->min_order_amount > 0 ? '₹' . number_format($coupon->min_order_amount) : '—' }}</td>
                                    <td>
                                        {{ $coupon->usages_count }}{{ $coupon->usage_limit ? ' / ' . $coupon->usage_limit : '' }}
                                        @if($coupon->first_order_only)
                                            <br><small class="text-muted">first order only</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($coupon->valid_from || $coupon->valid_to)
                                            {{ $coupon->valid_from?->format('d M Y') ?? 'any' }}
                                            &rarr;
                                            {{ $coupon->valid_to?->format('d M Y') ?? 'any' }}
                                        @else
                                            <span class="text-muted">Always</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $coupon->status_id ? 'bg-label-success' : 'bg-label-secondary' }}">
                                            {{ $coupon->status_id ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('ecommerce.coupons.edit', $coupon->id) }}"
                                           class="btn btn-sm btn-primary">
                                            <i class="icon-base ri ri-edit-line icon-16px"></i>
                                        </a>

                                        <form method="POST" action="{{ route('ecommerce.coupons.delete') }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Remove this coupon?')">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $coupon->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="icon-base ri ri-delete-bin-line icon-16px"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center py-4">No coupons yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{ $coupons->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
