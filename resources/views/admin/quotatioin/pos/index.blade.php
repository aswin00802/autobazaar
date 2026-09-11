@extends('admin.layouts.app')
@section('title')
    POS Quotation
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush


@section('content')
<div class="row">
    <!-- @can('add_pos_quotation')
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
        <a href="{{route('pos-quotation.create')}}" class="btn create-new btn-primary">
            <span>
                <span class="d-flex align-items-center">
                    <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                    <span class="d-none d-sm-inline-block">Create Quotation</span>
                </span>
            </span>
        </a>
    </div>
    @endcan -->
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-body">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>
                            <th>Quotation No</th>
                            <th>Customer Name</th>
                            <th>Phone Number</th>
                            <th>Quotation Auto ID</th>
                            <th>Brand Name</th>
                            <th>Model Name</th>
                            <th>Quotation Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($quotations))
                            @foreach ($quotations as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->quotation_no }}</td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->mobile }}</td>
                                    <td>
                                        <a href="{{route('auto-management.new-auto.view-details',Crypt::encryptString($item->auto_id))}}" target="_blank" class="btn btn-xs btn-twitter waves-effect waves-light text-white">
                                            <i class="icon-base ri ri-eye-line icon-16px me-2"></i>
                                            {{ $item->auto->auto_unique_id ?? null }}
                                        </a>
                                    </td>

                                    <td>{{ $item->auto->autoBrands->brand_name ?? null }}</td>
                                    <td>{{ $item->auto->autoModel->model_name ?? null }}</td>
                                    <td>{{ $item->created_at ?? 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-xs btn-twitter waves-effect waves-light text-white quotation-view" 
                                            data-posNo="{{$item->quotation_no}}"
                                            data-createDate="{{$item->created_at}}"
                                            data-name="{{$item->name}}"
                                            data-email="{{$item->email}}"
                                            data-mobile="{{$item->mobile}}"
                                            data-address="{{$item->address}}"
                                            data-brand="{{$item->auto->autoBrands->brand_name}}"
                                            data-model="{{$item->auto->autoModel->model_name}}"
                                            data-orp="{{$item->auto->orp}}"
                                            data-discount="{{$item->discount_amount}}"
                                            data-totalAmount="{{$item->total_amount}}"
                                            data-percentage="{{$item->loan_percentage}}"
                                            data-loanAmount="{{$item->total_loan_amount}}"
                                            data-interest="{{$item->interest}}"
                                            data-emiMonth="{{$item->emi_months}}"
                                            data-emiAmount="{{$item->emi_amount}}"
                                            data-downPayment="{{$item->down_payment}}"
                                            data-fitting="{{$item->fitting_fee}}"
                                            data-permit="{{$item->permit_fee}}"
                                            data-process="{{$item->loan_process_fee}}"
                                            data-gifts='@json($item->gifts)'
                                        >
                                            <i class="icon-base ri ri-eye-line icon-16px me-2"></i> More
                                        </button>
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

<div class="modal fade" id="pos_quotation_create" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form class="modal-content" name="quotation_form" id="quotation_form" method="post">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="backDropModalTitle">POS Quotation : <span id="q_no"></span> - <span id="q_date"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Name</label>
                            <input type="text" required class="form-control" name="name" id="name" value="{{ old('name') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Mobile</label>
                            <input type="text" required class="form-control" name="mobile" id="mobile" value="{{ old('mobile') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" id="address" rows="2">{{ old('address') }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Auto Brand</label>
                             <input type="text" class="form-control" required readonly name="brand" id="brand" value="{{ old('brand') }}">
                            @error('brand')
                                <span style="color:red;">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Brand Model</label>
                            <input type="text" class="form-control" required readonly name="model" id="model" value="{{ old('model') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">On Road Price</label>
                            <input type="number" class="form-control" required readonly name="orp" id="orp" value="{{ old('orp') }}" id="orp">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Discount Amount</label>
                            <input type="number" class="form-control" required name="discount_amount" id="discount_amount" step="0.01" value="{{ old('discount_amount') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total Amount</label>
                            <input type="number" class="form-control" required readonly name="total_amount" id="total_amount" step="0.01" value="{{ old('total_amount') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Loan Percentage %</label>
                            <input type="number" class="form-control" required name="loan_percentage" id="loan_percentage" step="0.01" value="{{ old('loan_percentage') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Total Loan Amount</label>
                            <input type="number" class="form-control" required name="total_loan_amount" readonly id="total_loan_amount" step="0.01" value="{{ old('total_loan_amount') }}">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Interest (%)</label>
                            <input type="number" class="form-control" required name="interest" id="interest" step="0.01" value="{{ old('interest') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">EMI Months</label>
                            <input type="number" class="form-control" required name="emi_months" id="emi_months" value="{{ old('emi_months') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">EMI Amount</label>
                            <input type="number" class="form-control" required name="emi_amount" readonly id="emi_amount" step="0.01" value="{{ old('emi_amount') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Down Payment</label>
                            <input type="number" class="form-control" required name="down_payment" readonly id="down_payment" step="0.01" value="{{ old('down_payment') }}">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
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
                                    <input type="number" class="form-control mt-1 fee-input w-50" id="{{ $fee }}Input" name="{{ $fee }}"
                                        placeholder="Enter {{ ucwords(str_replace('_',' ',$fee)) }}"
                                        value="{{ old($fee) }}"  step="0.01">
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Add on Free Gifts</label>
                            @php
                                $gifts = ['mat','side_cutter','auto_cover','jacky','seat_cover','engine_oil_(2l)'];
                                $selectedGifts = $quotation->gifts ?? []; // gifts from DB
                            @endphp

                            @foreach ($gifts as $gift)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="{{ $gift }}Gift" name="gifts[]" value="{{ $gift }}"
                                        {{ in_array($gift, $selectedGifts) ? 'checked' : '' }} >

                                    <label class="form-check-label" for="{{ $gift }}Gift">
                                        {{ ucwords(str_replace('_',' ',$gift)) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on("click", ".quotation-view", function(e) {
            e.preventDefault()
            let quotationNo = $(this).data('posno')
            let createAt    = $(this).data('createdate')
            let name        = $(this).data('name')
            let email       = $(this).data('email')
            let mobile      = $(this).data('mobile')
            let address     = $(this).data('address')
            let brand       = $(this).data('brand')
            let model       = $(this).data('model')
            let orp         = $(this).data('orp')
            let discount    = $(this).data('discount')
            let total       = $(this).data('totalamount')
            let pecentage   = $(this).data('percentage')
            let loanAmount  = $(this).data('loanamount')
            let interest    = $(this).data('interest')
            let emiMonth    = $(this).data('emimonth')
            let emiAmount   = $(this).data('emiamount')
            let downPayment = $(this).data('downpayment')
            let fitting     = $(this).data('fitting')
            let permit      = $(this).data('permit')
            let process     = $(this).data('process')
            let giftsFromDB = $(this).data('gifts') || [];

            $('#name').val(name)
            $('#email').val(email)
            $('#mobile').val(mobile)
            $('#address').val(address)
            $('#q_no').html(quotationNo)
            $('#q_date').html(createAt)
            $('#brand').val(brand)
            $('#model').val(model)
            $('#orp').val(orp)
            $('#discount_amount').val(discount)
            $('#total_amount').val(total)
            $('#loan_percentage').val(pecentage)
            $('#total_loan_amount').val(loanAmount)
            $('#interest').val(interest)
            $('#emi_months').val(emiMonth)
            $('#emi_amount').val(emiAmount)
            $('#down_payment').val(downPayment)
            setFeeValue('fitting_fee', fitting);
            setFeeValue('permit_fee', permit);
            setFeeValue('loan_process_fee', process);
            $('.form-check-input[name="gifts[]"]').each(function(){
                let val = $(this).val();
                $(this).prop('checked', giftsFromDB.includes(val));
            });
            $('#pos_quotation_create').modal('show')
        })

        function setFeeValue(feeName, value) {

            let checkbox = $('#' + feeName + 'Check');
            let input    = $('#' + feeName + 'Input');

            if (parseFloat(value) > 0) {
                checkbox.prop('checked', true);
                input.val(value);
            } else {
                checkbox.prop('checked', false);
                input.val('');
            }
        }
    });
</script>
@endpush