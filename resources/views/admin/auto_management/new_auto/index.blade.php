@extends('admin.layouts.app')
@section('title')
Auto Management / New Auto
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    @can('add_used_auto')
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
            <a href="{{route('auto-management.new-auto.create')}}" class="btn create-new btn-primary">
                <span>
                    <span class="d-flex align-items-center">
                        <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                        <span class="d-none d-sm-inline-block">Add New Auto</span>
                    </span>
                </span>
            </a>
        </div>
    @endcan
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Auto Unique ID</th>
                            <th>Auto Image</th>
                            <th>Brand</th>                                   
                            <th>Model</th>
                            <th>POS Quotation</th>
                            <th>More Details</th>
                            <th>Actions</th>
                            <th>Fuel Type</th>
                            <th>Passenger Capacity</th>
                            <th>On Road Price</th>  
                            <th>Millage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($new_autos))
                            @foreach($new_autos as $auto)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{$auto->auto_unique_id}}</td>
                                    <td>
                                        @if(isset($auto->image_1))
                                            <img src="{{ asset($auto->image_1) }}" style="width:100px;height:100px" class="img-fluid" alt="auto post" />
                                        @else
                                            
                                        @endif
                                    </td>
                                    <td>{{ $auto->autoBrands->brand_name??'' }}</td>
                                    <td>{{ $auto->autoModel->model_name??'' }}</td>
                                    <td>
                                        <button class="btn btn-danger pos-quotation" data-id="{{$auto->id}}" data-brand="{{$auto->autoBrands->brand_name}}" data-model="{{$auto->autoModel->model_name}}" data-orp="{{$auto->orp}}">Get Quotation</button>
                                    </td>
                                    <td>
                                        @can('view_details_new_auto')
                                        <a href="{{ route('auto-management.new-auto.view-details',Crypt::encryptString($auto->id)) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon" title="View"><i class="icon-base ri ri-eye-line icon-16px me-2"></i>More</a>
                                        @endcan
                                    </td>
                                    <td>
                                         @can('edit_new_auto')
                                            <a href="{{ route('auto-management.new-auto.edit',$auto->id) }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                        @endcan
                                        @can('delete_new_auto')
                                            <a class="btn btn-sm btn-text-danger text-danger rounded-pill btn-icon item-delete" data-id="{{$auto->id}}"><i class="icon-base ri ri-delete-bin-2-line icon-20px"></i></a>
                                        @endcan
                                    </td>
                                    <td>{{$auto->autoFueltype->name??'' }}</td>
                                    <td>{{$auto->passenger_capacity}}</td>
                                    <td>{{$auto->orp}}</td>
                                    <td>{{$auto->millage}}</td>
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
        <form class="modal-content" action="{{route('auto-management.new-auto.quotation')}}" name="quotation_form" id="quotation_form" method="post">
            @csrf
            <input type="hidden" id="autoId" name="autoId" value="">
            <input type="hidden" id="base_down_payment">
            <div class="modal-header">
                <h5 class="modal-title" id="backDropModalTitle">POS Quotation</h5>
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
                            <input type="number" class="form-control" required name="down_payment" id="down_payment" step="0.01" value="{{ old('down_payment') }}">
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
                                        value="{{ old($fee) }}" {{ old($fee) ? '' : 'disabled' }} step="0.01">
                                </div>
                            @endforeach
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Add on Free Gifts</label>
                            @php
                                $gifts = ['mat','side_cutter','auto_cover','jacky','seat_cover','engine_oil_(2l)'];
                            @endphp

                            @foreach ($gifts as $gift)
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="{{ $gift }}Gift" name="gifts[]" value="{{ $gift }}"
                                        {{ in_array($gift, old('gifts', [])) ? 'checked' : '' }} >

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
                <button type="submit" class="btn btn-primary">Get Quotation</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
<script src="{{asset('admin/assets/js/ui-modals.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $(document).on("click", ".pos-quotation", function(e) {
            e.preventDefault()
            let id = $(this).data('id')
            let brand = $(this).data('brand')
            let model = $(this).data('model')
            let orp = $(this).data('orp')
            $('#brand').val(brand)
            $('#model').val(model)
            $('#orp').val(orp)
            $('#autoId').val(id)
            $('#pos_quotation_create').modal('show')
        });

        $(document).on('click','.sell_auto',function(){
            let id = $(this).data('id')
            let unique_id = $(this).data('unique_id')
            $('#auto_id').text('')
            $('#post_id').val('')
            $('#auto_id').text(unique_id)
            $('#post_id').val(id)
            $('#auto_sell_model').modal('show');
        });
    
        $(document).on("click", ".item-delete", function() {
            let el = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to delete this Auto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-outline-secondary waves-effect'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    let id = el.data('id');
                    
                    $.ajax({
                        type:'POST',
                        url:"{{route('auto-management.new-auto.delete')}}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id:id,
                        },
                        beforeSend: function() {
                            $.blockUI({
                                message: '<div class="feather icon-refresh-cw icon-spin font-medium-2"></div>',
                                overlayCSS: {
                                    backgroundColor: '#FFF',
                                    opacity: 0.8,
                                    cursor: 'wait'
                                },
                                css: {
                                    border: 0,
                                    padding: 0,
                                    backgroundColor: 'transparent'
                                }
                            });
                        },
                        complete: function(response) {
                            $.unblockUI();
                        },
                        success: function (response) {
                            $.unblockUI();
                            if(response.success == true){
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: response.message,
                                    customClass: {
                                        confirmButton: 'btn btn-success waves-effect'
                                    }
                                }).then(function(){
                                    location.reload();
                                })
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: response.message,
                                    type: "error",
                                    confirmButtonClass: "btn btn-danger",
                                    buttonsStyling: !1
                                })
                            }
                        },
                        error: function (error) {
                            $.unblockUI();
                            alert('error; ' + eval(error));
                        }
                    });
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Deleted!',
                    //     text: 'Your file has been deleted.',
                    //     customClass: {
                    //     confirmButton: 'btn btn-success waves-effect'
                    //     }
                    // });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Cancelled',
                    text: 'Your email tempalte file is safe :)',
                    icon: 'error',
                    customClass: {
                    confirmButton: 'btn btn-success waves-effect'
                    }
                });
                }
            });
        })
        
    });
</script>
<script>
    document.querySelectorAll('.fee-checkbox').forEach(function(checkbox){
        checkbox.addEventListener('change', function(){
            const inputId = this.id.replace('Check','Input');
            const inputField = document.getElementById(inputId);
            inputField.disabled = !this.checked;
            calculateTotal();
        });
    });

    //discount calculation 
    function updateTotalAmount() {
        let baseAmount     = parseFloat(document.getElementById('orp').value) || 0;
        let discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;

        let totalAmount = baseAmount - discountAmount;
        // negative amount avoid
        if (totalAmount < 0) {
            totalAmount = 0;
        }

        document.getElementById('total_amount').value = totalAmount.toFixed(2);
    }
    document.getElementById('orp').addEventListener('input', updateTotalAmount);
    document.getElementById('discount_amount').addEventListener('input', updateTotalAmount);
</script>
<!-- total loan amount calculation -->
<script>
    const totalAmountInput     = document.getElementById('total_amount');
    const loanPercentageInput  = document.getElementById('loan_percentage');
    const totalLoanAmountInput = document.getElementById('total_loan_amount');

    function calculateLoanAmount() {

        let totalAmount    = parseFloat(totalAmountInput.value) || 0;
        let loanPercentage = parseFloat(loanPercentageInput.value) || 0;

        //Total amount not filled
        if (totalAmount <= 0) {
            Swal.fire({
                title: "Error!",
                text: "Please enter Total Amount first",
                icon: "error"
            }).then(() => {
                loanPercentageInput.value = '';
                totalLoanAmountInput.value = '';
                totalAmountInput.focus();
            });
            return;
            // alert('Please enter Total Amount first');
            
        }

        //Percentage limit
        if (loanPercentage > 100) {
            // alert('Loan percentage cannot exceed 100%');
            // loanPercentageInput.value = 100;
            // loanPercentage = 100;
            Swal.fire({
                title: "Warning!",
                text: "Loan percentage cannot exceed 100%",
                icon: "warning"
            });
            loanPercentageInput.value = 100;
            loanPercentage = 100;
        }

        let loanAmount = (totalAmount * loanPercentage) / 100;

        // totalLoanAmountInput.value = loanAmount.toFixed(2);
        totalLoanAmountInput.value = Math.round(loanAmount);

        document.getElementById('base_down_payment').value = Math.round(totalAmount - loanAmount);
        document.getElementById('down_payment').value = Math.round(totalAmount - loanAmount);

        calculateEmiAndDownPayment();
    }

    // Trigger when user types or clicks
    // loanPercentageInput.addEventListener('input', calculateLoanAmount);
    // loanPercentageInput.addEventListener('focus', calculateLoanAmount);
    loanPercentageInput.addEventListener('input', calculateLoanAmount);
</script>
<!-- emi amount and downpayment calculation -->
<script>
    const totalAmountInputs     = document.getElementById('total_amount');
    const totalLoanAmountInputs = document.getElementById('total_loan_amount');
    const interestInput         = document.getElementById('interest');
    const emiMonthsInput        = document.getElementById('emi_months');
    const emiAmountInput        = document.getElementById('emi_amount');
    const downPaymentInput      = document.getElementById('down_payment');

    function calculateEmiAndDownPayment() {

        let totalAmount     = parseFloat(totalAmountInputs.value) || 0;
        let totalLoanAmount = parseFloat(totalLoanAmountInputs.value) || 0;
        let interest        = parseFloat(interestInput.value);
        let emiMonths       = parseInt(emiMonthsInput.value);

        //total loan amount must exist
        if (totalLoanAmount <= 0) {
            alert('Please calculate Loan Amount first');
            interestInput.value = '';
            emiMonthsInput.value = '';
            return;
        }

        //interest & months mandatory
        if (!interest || !emiMonths) {
            return;
        }

        //EMI months validation
        if (emiMonths <= 0) {
            alert('EMI months must be greater than 0');
            emiMonthsInput.value = '';
            return;
        }

        //Monthly interest rate
        let monthlyRate = (interest / 100) / 12;
        
        //Correct EMI Formula
        let emiAmount = (totalLoanAmount * (1 + (monthlyRate * emiMonths))) / emiMonths;

        emiAmountInput.value   = Math.round(emiAmount);
        // downPaymentInput.value = Math.round(totalAmount - totalLoanAmount);
        let baseDownPayment = Math.round(totalAmount - totalLoanAmount);

        document.getElementById('base_down_payment').value = baseDownPayment;
        downPaymentInput.value = baseDownPayment;
    }

    interestInput.addEventListener('input', calculateEmiAndDownPayment);
    emiMonthsInput.addEventListener('input', calculateEmiAndDownPayment);
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const baseDownPaymentInput = document.getElementById('base_down_payment');
        const downPaymentInput    = document.getElementById('down_payment');

        function updateDownPaymentWithFees() {

            let baseDownPayment = parseFloat(baseDownPaymentInput.value) || 0;
            let extraTotal = 0;

            document.querySelectorAll('.fee-checkbox').forEach((checkbox) => {

                let feeInput = checkbox.closest('.form-check')
                                        .querySelector('.fee-input');

                if (checkbox.checked && feeInput.value) {
                    extraTotal += parseFloat(feeInput.value) || 0;
                }
            });

            downPaymentInput.value = Math.round(baseDownPayment + extraTotal);
        }

        // Checkbox toggle
        document.querySelectorAll('.fee-checkbox').forEach((checkbox) => {

            checkbox.addEventListener('change', function () {

                let feeInput = this.closest('.form-check')
                                .querySelector('.fee-input');

                if (this.checked) {
                    feeInput.disabled = false;
                    feeInput.focus();
                } else {
                    feeInput.value = '';
                    feeInput.disabled = true;
                }

                updateDownPaymentWithFees();
            });
        });

        // Fee amount typing
        document.querySelectorAll('.fee-input').forEach((input) => {
            input.addEventListener('input', updateDownPaymentWithFees);
        });

    });
</script>

<script>
    const downPaymentManualInput = document.getElementById('down_payment');

    function calculateLoanFromDownPayment() {

        let totalAmount = parseFloat(totalAmountInput.value) || 0;
        let enteredDownPayment = parseFloat(downPaymentManualInput.value) || 0;

        if (totalAmount <= 0) {
            alert('Total Amount required first');
            downPaymentManualInput.value = '';
            return;
        }

        // Calculate extra fees total
        let extraTotal = 0;
        document.querySelectorAll('.fee-checkbox').forEach((checkbox) => {

            let feeInput = checkbox.closest('.form-check')
                                    .querySelector('.fee-input');

            if (checkbox.checked && feeInput.value) {
                extraTotal += parseFloat(feeInput.value) || 0;
            }
        });

        // Base down payment (excluding fees)
        let baseDownPayment = enteredDownPayment - extraTotal;

        if (baseDownPayment < 0) {
            alert('Invalid Down Payment');
            return;
        }

        let loanAmount = totalAmount - baseDownPayment;

        if (loanAmount < 0) {
            alert('Down Payment cannot exceed Total Amount');
            return;
        }

        let loanPercentage = (loanAmount / totalAmount) * 100;

        // Set values
        totalLoanAmountInput.value = Math.round(loanAmount);
        loanPercentageInput.value  = loanPercentage.toFixed(2);

        document.getElementById('base_down_payment').value = Math.round(baseDownPayment);

        // Recalculate EMI automatically
        calculateEmiAndDownPayment();
    }

    // Trigger when manually typing Down Payment
    downPaymentManualInput.addEventListener('input', calculateLoanFromDownPayment);

</script>



@endpush