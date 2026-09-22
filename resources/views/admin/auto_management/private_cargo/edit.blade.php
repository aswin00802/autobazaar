@extends('admin.layouts.app')
@section('title')
Auto Management / Private Cargo Auto Update
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
        <a href="{{route('auto-management.private-cargo-auto')}}" class="btn btn-secondary" style="float:right">Back</a>
    </div>
</div>
<div class="row">
    <form method="post" action="{{ route('auto-management.private-cargo-auto.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="auto_usage_status" value="{{ $auto->auto_usage_status }}">
        <input type="hidden" name="id" value="{{ $auto->id }}">
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Private Cargo Auto Update</h4>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <div class="card">
                <div class="card-header text-center">
                    <h5>Private Cargo Auto Details</h5>
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
                                            <option value="{{ $brand->id }}" @selected($auto->auto_brand_id === $brand->id)>{{ $brand->brand_name }}</option>
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
                                    @if(!empty($models))
                                        @foreach ($models as $model)
                                            <option value="{{ $model->id }}" @selected($auto->auto_model_id === $model->id)>{{ $model->model_name }}</option>
                                        @endforeach
                                    @endif
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
                                            <option value="{{ $i }}" @selected((int)$auto->registration_year === $i)>{{ $i }}</option>
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
                                <input type="text" value="{{ old('registration_number',$auto->registration_number) }}" id="registration_number" class="form-control" placeholder="Enter Registration Number" name="registration_number">
                                @error('registration_number')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">RTO</label>
                                <input type="text" value="{{ old('rto',$auto->rto) }}" id="rto" class="form-control" placeholder="Enter RTO" name="rto">
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
                                            <option value="{{ $fuel->id }}" @selected($auto->fuel_type_id === $fuel->id)>{{ $fuel->name }}</option>
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
                                <input type="text" value="{{ old('kilometer',$auto->kilometer) }}" id="kilometer" class="form-control" placeholder="Enter Kilometer" name="kilometer">
                                @error('kilometer')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Price Expectations</label>
                                <input type="text" value="{{ old('price_expectations',$auto->price_expectations) }}" id="price_expectations" class="form-control" placeholder="Enter Price Expectations" name="price_expectations">
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
                                            <option value="{{ $owner->id }}" @selected((int)$auto->owner === $owner->id)>{{ $owner->name }}</option>
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
                                <input type="text" value="{{ old('name',$auto->name) }}" id="name" class="form-control" placeholder="Enter Owner Name" name="name">
                                @error('name')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                            <div class="mb-4">
                                <label class="form-label" for="">Owner Mobile No</label>
                                <input type="text" value="{{ old('mobile_number',$auto->mobile_number) }}" id="mobile_number" class="form-control" placeholder="Enter Owner Mobile No" name="mobile_number">
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
                                    <option value="available" @selected($auto->chellan_status === 'available')>Available</option>
                                    <option value="not_available" @selected($auto->chellan_status === 'not_available')>Not Available</option>
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
                                    <option value="available" @selected($auto->accident_status === 'available')>Available</option>
                                    <option value="not_available" @selected($auto->accident_status === 'not_available')>Not Available</option>
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
                                    <option value="available" @selected($auto->noc_status === 'available')>Available</option>
                                    <option value="not_available" @selected($auto->noc_status === 'not_available')>Not Available</option>
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
                                    <option value="available" @selected($auto->loan_status === 'available')>Available</option>
                                    <option value="not_available" @selected($auto->loan_status === 'not_available')>Not Available</option>
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
                                    <input class="form-check-input toggle-status" type="radio" name="fc_status" {{ old('fc_status', $auto->fc_status) == 'expired' ? 'checked' : '' }}  data-target="#fc_year_div" id="fc_expired" value="expired" />
                                    <label class="form-check-label" for="fc_expired">Expired</label>
                                </div>
                                @php
                                    $fcStatus = $auto->fc_status;
                                    $isExpired = $fcStatus === 'expired';
                                    $selectedYear = !$isExpired && is_numeric($fcStatus) ? $fcStatus : 'year';
                                @endphp
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input toggle-status" type="radio" {{ $auto->fc_status !== 'expired' ? 'checked' : '' }} name="fc_status" data-target="#fc_year_div" id="fc_year" value="{{ $selectedYear }}" />
                                    <label class="form-check-label" for="fc_year">Select Year</label>
                                </div>
                                @error('fc_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 {{ $auto->fc_status !== 'expired' ? '' : 'd-none' }}" id="fc_year_div">
                            <div class="mb-4">
                                <label class="form-label" for="">Select FC Year</label><br>
                                <select class="form-select" name="" id="fc_select_year">
                                    <option value="">Select FC Year</option>
                                    @for($f=date('Y');$f<=(date('Y') + 5) ;$f++)
                                        <option value="{{$f}}" {{ $auto->fc_status == $f ? 'selected' : '' }}>{{$f}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">Permit Status</label><br>
                                <div class="form-check form-check-inline mt-4">
                                    <input class="form-check-input toggle-status" type="radio" {{ old('permit_status', $auto->permit_status) == 'expired' ? 'checked' : '' }} data-target="#permit_year_div" name="permit_status" id="permit_expired" value="expired" />
                                    <label class="form-check-label" for="permit_expired">Expired</label>
                                </div>
                                @php
                                    $permitStatus = $auto->permit_status;
                                    $permit_isExpired = $permitStatus === 'expired';
                                    $permit_selectedYear = !$permit_isExpired && is_numeric($permitStatus) ? $permitStatus : 'year';
                                @endphp
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input toggle-status" type="radio" {{ $auto->permit_status !== 'expired' ? 'checked' : '' }} data-target="#permit_year_div" name="permit_status" id="permit_year" value="{{ $permit_selectedYear }}" />
                                    <label class="form-check-label" for="permit_year">Select Year</label>
                                </div>
                                @error('permit_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 {{ $auto->permit_status !== 'expired' ? '' : 'd-none' }}" id="permit_year_div">
                            <div class="mb-4">
                                <label class="form-label" for="">Select Permit Year</label><br>
                                <select class="form-select" name="" id="permit_select_year">
                                    <option value="">Select Permit Year</option>
                                    @for($f=date('Y');$f<=(date('Y') + 5) ;$f++)
                                        <option value="{{$f}}" {{ $auto->permit_status == $f ? 'selected' : '' }}>{{$f}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">Insurance</label><br>
                                <div class="form-check form-check-inline mt-4">
                                    <input class="form-check-input toggle-status" {{ old('insurance', $auto->insurance) == 'expired' ? 'checked' : '' }} type="radio" data-target="#insurance_year_div" name="insurance" id="insurance_expired" value="expired" />
                                    <label class="form-check-label" for="insurance_expired">Expired</label>
                                </div>
                                @php
                                    $insuranceStatus = $auto->insurance;
                                    $insurance_isExpired = $insuranceStatus === 'expired';
                                    $insurance_selectedYear = !$insurance_isExpired && is_numeric($insuranceStatus) ? $insuranceStatus : 'year';
                                @endphp
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input toggle-status" type="radio" {{ $auto->insurance !== 'expired' ? 'checked' : '' }} data-target="#insurance_year_div" name="insurance" id="insurance_year" value="{{ $insurance_selectedYear }}" />
                                    <label class="form-check-label" for="insurance_year">Select Year</label>
                                </div>
                                @error('loan_status')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 {{ $auto->insurance !== 'expired' ? '' : 'd-none' }}" id="insurance_year_div">
                            <div class="mb-4">
                                <label class="form-label" for="">Select Insurance Year</label><br>
                                <select class="form-select" name="" id="insurance_select_year">
                                    <option value="">Select Insurance Year</option>
                                    @for($f=date('Y');$f<=(date('Y') + 5) ;$f++)
                                        <option value="{{$f}}"{{  $auto->insurance == $f ? 'selected' : '' }}>{{$f}}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="col-12 col-sm-3 col-md-3 col-lg-3">
                            <div class="mb-4">
                                <label class="form-label" for="">RC Status </label><br>
                                <div class="form-check form-check-inline mt-4">
                                    <input class="form-check-input" type="radio" name="rc_status" {{ old('rc_status', $auto->rc_status) == 'yes' ? 'checked' : '' }} id="rc_status_yes" value="yes" />
                                    <label class="form-check-label" for="rc_status_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="rc_status" {{ old('rc_status', $auto->rc_status) == 'no' ? 'checked' : '' }} id="rc_status_no" value="no" />
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
                                <textarea class="form-control h-px-100" name="condition" id="condition" placeholder="Auto Condition here...">{{ old('condition',$auto->condition) }}</textarea>
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
                                    <input class="form-check-input financial_input" {{ $auto->financial_availability ? 'checked' : '' }} type="checkbox" data-class="financial" value="1" name="financial_availability" id="financial_availability" />
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
                                <input type="text" value="{{ old('loan_amount',$auto->loan_amount) }}" id="loan_amount" class="form-control" placeholder="Enter Loan Amount" name="loan_amount">
                                @error('loan_amount')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 financial d-none">
                            <div class="mb-4">
                                <label class="form-label" for="">First Payment</label>
                                <input type="text" value="{{ old('first_payment',$auto->first_payment) }}" id="first_payment" class="form-control" placeholder="Enter Loan First Payment" name="first_payment">
                                @error('first_payment')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 financial d-none">
                            <div class="mb-4">
                                <label class="form-label" for="">EMI Amount</label>
                                <input type="text" value="{{ old('emi_amount',$auto->emi_amount) }}" id="emi_amount" class="form-control" placeholder="Enter Loan EMI Amount" name="emi_amount">
                                @error('emi_amount')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-sm-3 col-md-3 col-lg-3 financial d-none">
                            <div class="mb-4">
                                <label class="form-label" for="">No Of Months</label>
                                <input type="text" value="{{ old('no_of_months',$auto->no_of_months) }}" id="no_of_months" class="form-control" placeholder="Enter Loan EMI No of Months" name="no_of_months">
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
                            @if(isset($auto->image_1))
                                <img src="{{ asset($auto->image_1) }}" style="width:200px;height:200px" class="img-fluid" alt="auto post" />
                            @endif
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 2</label>
                                <input type="file" value="{{ old('image_2') }}" id="image_2" class="form-control" name="image_2">
                                @error('image_2')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                            @if(isset($auto->image_2))
                                <img src="{{ asset($auto->image_2) }}" style="width:200px;height:200px" class="img-fluid" alt="auto post" />
                            @endif
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 3</label>
                                <input type="file" value="{{ old('image_3') }}" id="image_3" class="form-control" name="image_3">
                                @error('image_3')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                            @if(isset($auto->image_3))
                                <img src="{{ asset($auto->image_3) }}" style="width:200px;height:200px" class="img-fluid" alt="auto post" />
                            @endif
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 4</label>
                                <input type="file" value="{{ old('image_4') }}" id="image_4" class="form-control" name="image_4">
                                @error('image_4')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                            @if(isset($auto->image_4))
                                <img src="{{ asset($auto->image_4) }}" style="width:200px;height:200px" class="img-fluid" alt="auto post" />
                            @endif
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 5</label>
                                <input type="file" value="{{ old('image_5') }}" id="image_5" class="form-control" name="image_5">
                                @error('image_5')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                            @if(isset($auto->image_5))
                                <img src="{{ asset($auto->image_5) }}" style="width:200px;height:200px" class="img-fluid" alt="auto post" />
                            @endif
                        </div>
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 mb-2">
                           <div class="mb-4">
                                <label class="form-label" for="">image 6</label>
                                <input type="file" value="{{ old('image_6') }}" id="image_6" class="form-control" name="image_6">
                                @error('image_6')
                                    <span style="color:red;">{{ $message }}</span>
                                @enderror
                            </div> 
                            @if(isset($auto->image_6))
                                <img src="{{ asset($auto->image_6) }}" style="width:200px;height:200px" class="img-fluid" alt="auto post" />
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
            <button class="btn btn-info" style="float:right">Update</button>
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