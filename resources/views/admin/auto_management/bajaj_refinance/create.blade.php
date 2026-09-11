@extends('admin.layouts.app')
@section('title')
Auto Management / Bajaj ReFinance Auto Create
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
        <a href="{{route('auto-management.bajaj-refinance-auto')}}" class="btn btn-secondary" style="float:right">Back</a>
    </div>
</div>
<div class="row">
    <form method="post" action="{{ route('auto-management.bajaj-refinance-auto.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="auto_usage_status" value="bajaj_refinance">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Bajaj ReFinance Auto Create</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Bajaj ReFinance Auto Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Auto Brand</label>
                                <select class="form-select" name="auto_brand_id" id="auto_brand_id">
                                    <option value="">Select Brands</option>
                                    @if(!empty($brands))
                                        @foreach ($brands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('auto_brand_id')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Model</label>
                                <select class="form-select" name="auto_model_id" id="auto_model_id">
                                    <option value="">Select Model</option>
                                    <!-- @if(!empty($models))
                                        @foreach ($models as $model)
                                            <option value="{{ $model->id }}">{{ $model->model_name }}</option>
                                        @endforeach
                                    @endif -->
                                </select>
                                @error('auto_model_id')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Registration Year</label>
                                <select class="form-select" name="registration_year" id="registration_year">
                                    <option value="">Select Registration Year</option>
                                        @for ($i = 2000 ; $i<=date('Y'); $i++)
                                            <option value="{{ $i }}">{{ $i }}</option>
                                        @endfor
                                </select>
                                @error('registration_year')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Registration Number</label>
                                <input type="text" value="{{ old('registration_number') }}" id="registration_number" class="form-control" placeholder="Enter Registration Number" name="registration_number">
                                @error('registration_number')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">RTO</label>
                                <input type="text" value="{{ old('rto') }}" id="rto" class="form-control" placeholder="Enter RTO" name="rto">
                                @error('rto')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Fuel Type</label>
                                <select class="form-select" name="fuel_type_id" id="fuel_type_id">
                                    <option value="">Select Fuel Type</option>
                                    @if(!empty($fuels))
                                        @foreach ($fuels as $fuel)
                                            <option value="{{ $fuel->id }}">{{ $fuel->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('fuel_type_id')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Kilometer</label>
                                <input type="text" value="{{ old('kilometer') }}" id="kilometer" class="form-control" placeholder="Enter Kilometer" name="kilometer">
                                @error('kilometer')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Price Expectations</label>
                                <input type="text" value="{{ old('price_expectations') }}" id="price_expectations" class="form-control" placeholder="Enter Price Expectations" name="price_expectations">
                                @error('price_expectations')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Owner Details</h5>
                </div>
                <div class="card-body">
                    <div class = "row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Owner</label>
                                <select class="form-select" name="owner" id="owner">
                                    <option value="">Select Owner</option>
                                    @if(!empty($owners))
                                        @foreach ($owners as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('owner')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Owner Name</label>
                                <input type="text" value="{{ old('name') }}" id="name" class="form-control" placeholder="Enter Owner Name" name="name">
                                @error('name')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Owner Mobile No</label>
                                <input type="text" value="{{ old('mobile_number') }}" id="mobile_number" class="form-control" placeholder="Enter Owner Mobile No" name="mobile_number">
                                @error('mobile_number')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Auto Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">Chellan Status</label>
                                <select class="form-select" name="chellan_status" id="chellan_status">
                                    <option value="">Select Chellan Status</option>
                                    <option value="available">Available</option>
                                    <option value="not_available">Not Available</option>
                                </select>
                                @error('chellan_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">Accident Status</label>
                                <select class="form-select" name="accident_status" id="accident_status">
                                    <option value="">Select Accident Status</option>
                                    <option value="available">Available</option>
                                    <option value="not_available">Not Available</option>
                                </select>
                                @error('accident_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">NOC Status</label>
                                <select class="form-select" name="noc_status" id="noc_status">
                                    <option value="">Select NOC Status</option>
                                    <option value="available">Available</option>
                                    <option value="not_available">Not Available</option>
                                </select>
                                @error('noc_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">Loan Status</label>
                                <select class="form-select" name="loan_status" id="loan_status">
                                    <option value="">Select Loan Status</option>
                                    <option value="available">Available</option>
                                    <option value="not_available">Not Available</option>
                                </select>
                                @error('loan_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">FC Status</label><br>
                                <div class="form-check form-check-inline mt-4">
                                    <input class="form-check-input toggle-status" type="radio" name="fc_status"  data-target="#fc_year_div" id="fc_expired" value="expired" />
                                    <label class="form-check-label" for="fc_expired">Expired</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input toggle-status" type="radio" name="fc_status" data-target="#fc_year_div" id="fc_year" value="year" />
                                    <label class="form-check-label" for="fc_year">Select Year</label>
                                </div>
                                @error('fc_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 d-none" id="fc_year_div">
                            <div class="mb-4">
                                <label class="form-label" for="">Select FC Year</label><br>
                                <select class="form-select" name="" id="fc_select_year">
                                    <option value="">Select FC Year</option>
                                    @for($f=date('Y');$f<=(date('Y') + 5) ;$f++)
                                        <option value="{{$f}}">{{$f}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">Permit Status</label><br>
                                <div class="form-check form-check-inline mt-4">
                                    <input class="form-check-input toggle-status" type="radio" data-target="#permit_year_div" name="permit_status" id="permit_expired" value="expired" />
                                    <label class="form-check-label" for="permit_expired">Expired</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input toggle-status" type="radio" data-target="#permit_year_div" name="permit_status" id="permit_year" value="year" />
                                    <label class="form-check-label" for="permit_year">Select Year</label>
                                </div>
                                @error('permit_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 d-none" id="permit_year_div">
                            <div class="mb-4">
                                <label class="form-label" for="">Select Permit Year</label><br>
                                <select class="form-select" name="" id="permit_select_year">
                                    <option value="">Select Permit Year</option>
                                    @for($f=date('Y');$f<=(date('Y') + 5) ;$f++)
                                        <option value="{{$f}}">{{$f}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">Insurance</label><br>
                                <div class="form-check form-check-inline mt-4">
                                    <input class="form-check-input toggle-status" type="radio" data-target="#insurance_year_div" name="insurance" id="insurance_expired" value="expired" />
                                    <label class="form-check-label" for="insurance_expired">Expired</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input toggle-status" type="radio" data-target="#insurance_year_div" name="insurance" id="insurance_year" value="year" />
                                    <label class="form-check-label" for="insurance_year">Select Year</label>
                                </div>
                                @error('loan_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 d-none" id="insurance_year_div">
                            <div class="mb-4">
                                <label class="form-label" for="">Select Insurance Year</label><br>
                                <select class="form-select" name="" id="insurance_select_year">
                                    <option value="">Select Insurance Year</option>
                                    @for($f=date('Y');$f<=(date('Y') + 5) ;$f++)
                                        <option value="{{$f}}">{{$f}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">RC Status </label><br>
                                <div class="form-check form-check-inline mt-4">
                                    <input class="form-check-input" type="radio" name="rc_status" id="rc_status_yes" value="yes" />
                                    <label class="form-check-label" for="rc_status_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rc_status" id="rc_status_no" value="no" />
                                    <label class="form-check-label" for="rc_status_no">No</label>
                                </div>
                                @error('loan_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Condition</label>
                                <textarea class="form-control h-px-100" name="condition" id="condition" placeholder="Auto Condition here...">{{ old('condition') }}</textarea>
                                @error('condition')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Financial Availability Status</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                            <input type="hidden" name="financial_availability" value="0">
                            <div class="mb-4">
                                <div class="form-check form-check-primary mt-4">
                                    <input class="form-check-input financial_input" type="checkbox" data-class="financial" value="1" name="financial_availability" id="financial_availability" />
                                    <label class="form-check-label" for="financial_availability">Financial Availability</label>
                                </div>
                                @error('image_1')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 financial d-none">
                            <div class="mb-4">
                                <label class="form-label" for="">Loan Amount</label>
                                <input type="text" value="{{ old('loan_amount') }}" id="loan_amount" class="form-control" placeholder="Enter Loan Amount" name="loan_amount">
                                @error('loan_amount')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 financial d-none">
                            <div class="mb-4">
                                <label class="form-label" for="">First Payment</label>
                                <input type="text" value="{{ old('first_payment') }}" id="first_payment" class="form-control" placeholder="Enter Loan First Payment" name="first_payment">
                                @error('first_payment')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 financial d-none">
                            <div class="mb-4">
                                <label class="form-label" for="">EMI Amount</label>
                                <input type="text" value="{{ old('emi_amount') }}" id="emi_amount" class="form-control" placeholder="Enter Loan EMI Amount" name="emi_amount">
                                @error('emi_amount')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 financial d-none">
                            <div class="mb-4">
                                <label class="form-label" for="">No Of Months</label>
                                <input type="text" value="{{ old('no_of_months') }}" id="no_of_months" class="form-control" placeholder="Enter Loan EMI No of Months" name="no_of_months">
                                @error('no_of_months')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Upload Images</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 1 <span class="invalid-message"> (Must be 500x500px)</span></label>
                                <input type="file" value="{{ old('image_1') }}" id="image_1" class="form-control" name="image_1">
                                @error('image_1')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 2</label>
                                <input type="file" value="{{ old('image_2') }}" id="image_2" class="form-control" name="image_2">
                                @error('image_2')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 3</label>
                                <input type="file" value="{{ old('image_3') }}" id="image_3" class="form-control" name="image_3">
                                @error('image_3')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 4</label>
                                <input type="file" value="{{ old('image_4') }}" id="image_4" class="form-control" name="image_4">
                                @error('image_4')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 5</label>
                                <input type="file" value="{{ old('image_5') }}" id="image_5" class="form-control" name="image_5">
                                @error('image_5')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 6</label>
                                <input type="file" value="{{ old('image_6') }}" id="image_6" class="form-control" name="image_6">
                                @error('image_6')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <button class="btn btn-info" style="float:right">Create</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){

        $(".financial_input").on("change", function(){

            let targetClass = $(this).data("class");
            if($(this).is(":checked")){
                $("." + targetClass).removeClass('d-none').addClass('d-block');
            } else {
                $("." + targetClass).removeClass('d-block').addClass('d-none');
            }
        });

        $(".financial_input:checked").each(function () {
            $(this).trigger("change");
        });


        $('.toggle-status').on('change', function(){
            var target = $(this).data('target');
            if ($(this).val() === 'year') {
                $(target).removeClass('d-none').addClass('d-block');
            } else {
                $(target).removeClass('d-block').addClass('d-none');
                $(target).find('select').val(''); // reset dropdown
            }
        });

        // dropdown change → set value to radio
        $('#fc_select_year, #permit_select_year, #insurance_select_year').on('change', function () {
            var yearVal = $(this).val();

            if (yearVal) {
                // find matching radio by name
                var targetRadio = $(this).closest('.col-12').prev().find('input[value="year"]');
                
                // update radio value to year & check it
                targetRadio.val(yearVal).prop('checked', true);
            } else {
                // if reset → make radio back to "year"
                var targetRadio = $(this).closest('.col-12').prev().find('input[value]');
                targetRadio.val('year').prop('checked', false);
            }
        });

        $('#auto_brand_id').on('change',function(){
            let brand_id = $('#auto_brand_id').val()
            $('#auto_model_id').empty()
            $('#auto_model_id').append('<option>Select Brand Model</option>')
            $.ajax({
                type:'POST',
                url:"{{route('brand.get-model')}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    brand_id:brand_id,
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
                        $.each(response.data, function(index, item) {
                            $('#auto_model_id').append('<option value="'+item.id+'">'+item.model_name+'</option>')
                        });
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
        })
    });
</script>
@endpush