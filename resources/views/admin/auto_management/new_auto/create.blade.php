@extends('admin.layouts.app')
@section('title')
Auto Management / Used Auto Create
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
        <a href="{{route('auto-management.new-auto')}}" class="btn btn-secondary" style="float:right">Back</a>
    </div>
</div>
<div class="row">
    <form method="post" action="{{ route('auto-management.new-auto.store') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="auto_usage_status" value="new_auto">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h4>New Auto Create</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h5>New Auto Details</h5>
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
                                <label class="form-label" for="">On-Road Price</label>
                                <input type="text" value="{{ old('orp') }}" id="orp" class="form-control" placeholder="Enter On Road Price" name="orp">
                                @error('orp')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Passenger Capacity</label>
                                <input type="text" value="{{ old('passenger_capacity') }}" id="passenger_capacity" class="form-control" placeholder="Enter Passenger Capacity" name="passenger_capacity">
                                @error('passenger_capacity')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Gear</label>
                                <input type="text" value="{{ old('gear') }}" id="gear" class="form-control" placeholder="Enter Gear" name="gear">
                                @error('gear')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Millage</label>
                                <input type="text" value="{{ old('millage') }}" id="millage" class="form-control" placeholder="Enter Millage" name="millage">
                                @error('millage')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Engine CC</label>
                                <input type="text" value="{{ old('engine_cc') }}" id="engine_cc" class="form-control" placeholder="Enter Engine CC" name="engine_cc">
                                @error('engine_cc')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Free Service</label>
                                <input type="text" value="{{ old('free_service') }}" id="free_service" class="form-control" placeholder="Enter Free Service" name="free_service">
                                @error('free_service')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Finance Arrangements</label>
                                <input type="text" value="{{ old('finance_arrangements') }}" id="finance_arrangements" class="form-control" placeholder="Enter Finance Arrangements" name="finance_arrangements">
                                @error('finance_arrangements')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Vehicle Suitable For</label>
                                <input type="text" value="{{ old('vehicle_suitable') }}" id="vehicle_suitable" class="form-control" placeholder="Enter Vehicle Suitable" name="vehicle_suitable">
                                @error('vehicle_suitable')
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
                                @error('financial_availability')
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
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
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