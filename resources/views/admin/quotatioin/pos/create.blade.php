@extends('admin.layouts.app')
@section('title')
    POS Quotation Create
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
@endpush


@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
        <a href="{{route('pos-quotation')}}" class="btn btn-secondary" style="float:right">Back</a>
    </div>
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="" method="POST" id="posForm">
                    @csrf
                    <input type="hidden" name="autoId" id="autoId">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile</label>
                            <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2">{{ old('address') }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Auto Brand</label>
                            <select id="brand" name="brand" class="select2 form-select" data-allow-clear="true">
                                <option value="">Select State</option>
                                @if(!empty($brands))
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand['auto_brand_id'] }}" {{ old('brand') == $brand['auto_brand_id'] ? 'selected' : '' }}>
                                            {{ $brand->autoBrands->brand_name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('brand')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Brand Model</label>
                            <input type="text" class="form-control" name="model" value="{{ old('model') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">On Road Price</label>
                            <input type="number" class="form-control" name="orp" value="{{ old('orp') }}" id="orp">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Discount Amount</label>
                            <input type="number" class="form-control" name="discount_amount" step="0.01" value="{{ old('discount_amount') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total Amount</label>
                            <input type="number" class="form-control" name="total_amount" step="0.01" value="{{ old('total_amount') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Loan %</label>
                            <input type="number" class="form-control" name="loan_percentage" step="0.01" value="{{ old('loan_percentage') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total Loan Amount</label>
                            <input type="number" class="form-control" name="total_loan_amount" step="0.01" value="{{ old('total_loan_amount') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Interest (%)</label>
                            <input type="number" class="form-control" name="interest" step="0.01" value="{{ old('interest') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">EMI Months</label>
                            <input type="number" class="form-control" name="emi_months" value="{{ old('emi_months') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">EMI Amount</label>
                            <input type="number" class="form-control" name="emi_amount" step="0.01" value="{{ old('emi_amount') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Down Payment</label>
                            <input type="number" class="form-control" name="down_payment" step="0.01" value="{{ old('down_payment') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Additional Fees</label>
                        @php
                            $fees = ['fitting_fee','permit_fee','loan_process_fee'];
                        @endphp

                        @foreach ($fees as $fee)
                            <div class="form-check mt-2">
                                <input class="form-check-input fee-checkbox" type="checkbox" id="{{ $fee }}Check"
                                    {{ old($fee) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $fee }}Check">
                                    {{ ucwords(str_replace('_',' ',$fee)) }}
                                </label>
                                <input type="number" class="form-control mt-1 fee-input" id="{{ $fee }}Input" name="{{ $fee }}"
                                    placeholder="Enter {{ ucwords(str_replace('_',' ',$fee)) }}"
                                    value="{{ old($fee) }}" {{ old($fee) ? '' : 'disabled' }} step="0.01">
                            </div>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('admin/assets/js/forms-selects.js')}}"></script>
@endpush