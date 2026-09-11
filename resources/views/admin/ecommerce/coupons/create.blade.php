@extends('admin.layouts.app')
@section('title')
E-commerce / Add Coupon
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <h5 class="card-header">Add Coupon</h5>

            <div class="card-body">
                <form method="POST" action="{{ route('ecommerce.coupons.store') }}">
                    @csrf

                    @include('admin.ecommerce.coupons._form')

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="{{ route('ecommerce.coupons') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
