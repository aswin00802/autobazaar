@extends('admin.layouts.app')
@section('title')
Enquiry Auto List
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="row">
            <div class="nav-align-top">
                <ul class="nav nav-pills mb-4 nav-fill" role="tablist">
                    <li class="nav-item mb-1 mb-sm-0">
                        <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-home" aria-controls="navs-pills-justified-home" aria-selected="true">
                            <span class="d-none d-sm-inline-flex align-items-center">
                            <i class="icon-base ri ri-user-voice-fill icon-sm me-2"></i>Used Auto Enquiry</span>
                            <i class="icon-base ri ri-user-voice-fill icon-sm d-sm-none"></i>
                        </button>
                    </li>
                    <li class="nav-item mb-1 mb-sm-0">
                        <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-justified-profile" aria-controls="navs-pills-justified-profile" aria-selected="false">
                            <span class="d-none d-sm-inline-flex align-items-center">
                            <i class="icon-base ri ri-user-voice-line icon-sm me-2"></i>New Auto Enquiry</span>
                            <i class="icon-base ri ri-user-voice-line icon-sm d-sm-none"></i>
                        </button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show text-nowrap active" id="navs-pills-justified-home" role="tabpanel">
                        <table class="datatables-fixed1 table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Sl.no</th>
                                    <th>Customer Name</th>
                                    <th>Phone Number</th>
                                    <th>Enquired Area</th>
                                    <th>Enquired Auto ID</th>
                                    <th>Brand Name</th>
                                    <th>Enquired Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($used_auto_enquirys))
                                    @foreach ($used_auto_enquirys as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ $item->user->phone_number }}</td>
                                            <td>{{ $item->user->autoAreas->name ?? 'N/A' }}</td>
                                            <td>
                                                <button type="button" class="btn btn-primary auto-details-btn"
                                                    data-bs-toggle="modal" data-bs-target="#exampleModal"
                                                    data-auto-id="{{ $item->auto->auto_unique_id }}"
                                                    data-brand="{{ $item->auto->autoBrands->brand_name ?? 'N/A' }}"
                                                    data-model="{{ $item->auto->specific_model ?? 'N/A' }}"
                                                    data-year="{{ $item->auto->registration_year ?? 'N/A' }}"
                                                    data-reg-number="{{ $item->auto->registration_number ?? 'N/A' }}"
                                                    data-fuel="{{ $item->auto->fuel_type ?? 'N/A' }}"
                                                    data-km="{{ $item->auto->kilometer ?? 'N/A' }}"
                                                    data-price="{{ $item->auto->price_expectations ?? 'N/A' }}"
                                                    data-owner="{{ $item->auto->owner ?? 'N/A' }}"
                                                    data-name="{{ $item->auto->name ?? 'N/A' }}"
                                                    data-chellan_status="{{ $item->auto->chellan_status ?? 'N/A' }}"
                                                    data-accident_status="{{ $item->auto->accident_status ?? 'N/A' }}"
                                                    data-rc_status="{{ $item->auto->rc_status ?? 'N/A' }}"
                                                    data-noc_status="{{ $item->auto->noc_status ?? 'N/A' }}"
                                                    data-loan_status="{{ $item->auto->loan_status ?? 'N/A' }}"
                                                    data-fc_status="{{ $item->auto->fc_status ?? 'N/A' }}"
                                                    data-permit_status="{{ $item->auto->permit_status ?? 'N/A' }}"
                                                    data-insurance="{{ $item->auto->insurance ?? 'N/A' }}"
                                                    data-rto="{{ $item->auto->rto ?? 'N/A' }}"
                                                    data-condition="{{ $item->auto->condition ?? 'N/A' }}"
                                                    data-loan_amount="{{ $item->auto->loan_amount ?? 'N/A' }}"
                                                    data-first_payment="{{ $item->auto->first_payment ?? 'N/A' }}"
                                                    data-emi_amount="{{ $item->auto->emi_amount ?? 'N/A' }}"
                                                    data-no_of_months="{{ $item->auto->no_of_months ?? 'N/A' }}"
                                                    data-financial_availability="{{ $item->auto->financial_availability ?? 'N/A' }}"

                                                    data-image_1="{{ $item->auto->image_1 ? asset($item->auto->image_1) : 'Image Not Available' }}"
                                                    data-image_2="{{ $item->auto->image_2 ? asset($item->auto->image_2) : 'Image Not Available' }}"
                                                    data-image_3="{{ $item->auto->image_3 ? asset($item->auto->image_3) : 'Image Not Available' }}"
                                                    data-image_4="{{ $item->auto->image_4 ? asset($item->auto->image_4) : 'Image Not Available' }}"
                                                    data-image_5="{{ $item->auto->image_5 ? asset($item->auto->image_5) : 'Image Not Available' }}"
                                                    data-image_6="{{ $item->auto->image_6 ? asset($item->auto->image_6) : 'Image Not Available' }}">


                                                    {{ $item->auto->auto_unique_id }}
                                                </button>
                                            </td>


                                            <td>{{ $item->auto->autoBrands->brand_name ?? null }}</td>
                                            <td>{{ $item->created_at ?? 'N/A' }}</td>
                                            <td>
                                                @if($item->interested_status == 1)
                                                    <span class="badge rounded-pill text-bg-danger">Pending</span>
                                                @elseif($item->interested_status == 2)
                                                    <span class="badge rounded-pill text-bg-success">Contacted</span>
                                                @elseif($item->interested_status == 3)
                                                    <span class="badge rounded-pill text-bg-secondary">Follow-up</span>
                                                @elseif($item->interested_status == 4)
                                                    <span class="badge rounded-pill text-bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- <button type="button" class="btn btn-sm btn-text-info text-info rounded-pill btn-icon item-update" data-id ="{{$item->id}}" data-bs-toggle="modal" data-bs-target="#update_status"><i class="icon-base ri ri-file-edit-line icon-20px"></i></button> -->
                                                @can('auto_enquiry_update')
                                                    <button type="button" class="btn btn-primary status_update_btn" data-id ="{{$item->id}}"><i class="icon-base ri ri-file-edit-line icon-20px"></i></button>
                                                @endcan
                                            </td>

                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade text-nowrap" id="navs-pills-justified-profile" role="tabpanel">
                        <table class="datatables-fixed table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Sl.no</th>
                                    <th>Customer Name</th>
                                    <th>Phone Number</th>
                                    <th>Enquired Area</th>
                                    <th>Enquired Auto ID</th>
                                    <th>Brand Name</th>
                                    <th>Enquired Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($new_auto_enquirys))
                                    @foreach ($new_auto_enquirys as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->user->name }}</td>
                                            <td>{{ $item->user->phone_number }}</td>
                                            <td>{{ $item->user->autoAreas->name ?? 'N/A' }}</td>

                                            <td>
                                                <button type="button" class="btn btn-primary new-auto-details-btn"
                                                    data-bs-toggle="modal" data-bs-target="#newAutoModal"
                                                    data-auto-id="{{ $item->auto->auto_unique_id }}"
                                                    data-brand1="{{ $item->auto->autoBrands->brand_name ?? 'N/A' }}"
                                                    data-model1="{{ $item->auto->specific_model ?? 'N/A' }}"
                                                    data-orp="{{ $item->auto->orp ?? 'N/A' }}"
                                                    data-fuel1="{{ $item->auto->autoFueltype->name ?? 'N/A' }}"
                                                    data-passenger="{{ $item->auto->passenger_capacity ?? 'N/A' }}"
                                                    data-gear="{{ $item->auto->gear ?? 'N/A' }}"
                                                    data-millage="{{ $item->auto->millage ?? 'N/A' }}"
                                                    data-engine="{{ $item->auto->engine_cc ?? 'N/A' }}"
                                                    data-free-service="{{ $item->auto->free_service ?? 'N/A' }}"
                                                    data-finance="{{ $item->auto->finance_arrangements ?? 'N/A' }}"
                                                    data-vechicle1="{{ $item->auto->vehicle_suitable ?? 'N/A' }}"
                                                    data-image_1="{{ $item->auto->image_1 ? asset($item->auto->image_1) : 'Image Not Available' }}"
                                                    data-image_2="{{ $item->auto->image_2 ? asset($item->auto->image_2) : 'Image Not Available' }}"
                                                    data-image_3="{{ $item->auto->image_3 ? asset($item->auto->image_3) : 'Image Not Available' }}"
                                                    data-image_4="{{ $item->auto->image_4 ? asset($item->auto->image_4) : 'Image Not Available' }}"
                                                    data-image_5="{{ $item->auto->image_5 ? asset($item->auto->image_5) : 'Image Not Available' }}"
                                                    data-image_6="{{ $item->auto->image_6 ? asset($item->auto->image_6) : 'Image Not Available' }}">
                                                    {{ $item->auto->auto_unique_id }}
                                                </button>
                                            </td>
                                            <td>{{ $item->auto->autoBrands->brand_name ?? null }}</td>
                                            <td>{{ $item->created_at ?? 'N/A' }}</td>
                                            <td>
                                                @if($item->interested_status == 1)
                                                    <span class="badge rounded-pill text-bg-danger">Pending</span>
                                                @elseif($item->interested_status == 2)
                                                    <span class="badge rounded-pill text-bg-success">Contacted</span>
                                                @elseif($item->interested_status == 3)
                                                    <span class="badge rounded-pill text-bg-secondary">Follow-up</span>
                                                @elseif($item->interested_status == 4)
                                                    <span class="badge rounded-pill text-bg-danger">Rejected</span>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- <a class="btn btn-sm btn-text-info text-info rounded-pill btn-icon item-update" data-id ="{{$item->id}}"><i class="icon-base ri ri-file-edit-line icon-20px"></i></a> -->
                                                @can('auto_enquiry_update')
                                                    <button type="button" class="btn btn-primary status_update_btn" data-id ="{{$item->id}}"><i class="icon-base ri ri-file-edit-line icon-20px"></i></button>
                                                @endcan
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
    </div>
</div>

<!-- used auto model -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header text-center fw-bold h4">
                        <p id="modal-auto-id"></p>
                    </div>
                    <div class="card-body row">

                        <div class="col-md-6 mb-2">
                            <label for="specific_model">Brand Name</label>
                            <input type="text" id="brand_name" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="specific_model">Model</label>
                            <input type="text" id="specific_modal" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="registration_year">Registration Year</label>
                            <input type="number" id="registration_year" name="registration_year"
                                class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="registration_number">Registration Number</label>
                            <input type="text" id="reg_number" name="registration_number"
                                class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="registration_number">Fuel</label>
                            <input type="text" id="fuel_type" name="fuel_type"
                                class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="registration_number">Kilometer</label>
                            <input type="text" id="km" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="registration_number">Price Expectations</label>
                            <input type="text" id="price" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header text-center fw-bold h4">
                        <p id="modal-auto-id">Auto Owner Details</p>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-6 mb-2">
                            <label for="owner">Owner</label>
                            <input type="text" id="owner" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="name">Name</label>
                            <input type="text" id="name" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header text-center fw-bold h4">
                        <p id="modal-auto-id">Auto Status</p>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-6 mb-2">
                            <label for="chellan_status">Chellan Status</label>
                            <input type="text" id="chellan_status" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="accident_status">Accident Status</label>
                            <input type="text" id="accident_status" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="rc_status">RC Status</label>
                            <input type="text" id="rc_status" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="noc_status">NOC Status</label>
                            <input type="text" id="noc_status" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="loan_status">Loan Status</label>
                            <input type="text" id="loan_status" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="fc_status">FC Status</label>
                            <input type="text" id="fc_status" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="permit_status">Permit Status</label>
                            <input type="text" id="permit_status" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="insurance">Insurance</label>
                            <input type="text" id="insurance" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="rto">RTO</label>
                            <input type="text" id="rto" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="condition">Condition</label>
                            <input type="text" id="condition" class="form-control" readonly>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header text-center fw-bold h4">
                        <p id="modal-auto-id">Auto Owner Details</p>
                    </div>
                    <div class="card-body row">
                        <div class="col-12">
                            <input type="checkbox" id="financial_availability"
                                name="financial_availability" class="form-check-input" value="true"
                                data-financial_availability="{{ $item->auto->financial_availability ?? 'false' }}">

                            <label for="financial_availability">Financial Availability Active</label>

                            <input type="hidden" id="financial_availability_hidden"
                                name="financial_availability_hidden" value="false">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="loan_amount">Loan Amount</label>
                            <input type="text" id="loan_amount" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="first_payment">Initial Payment</label>
                            <input type="text" id="first_payment" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="emi_amount">EMI Amount</label>
                            <input type="text" id="emi_amount" class="form-control" readonly>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label for="no_of_months">No. Of Months</label>
                            <input type="text" id="no_of_months" class="form-control" readonly>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header text-center fw-bold h4">
                        <p id="modal-auto-id"></p>
                        <div class="card-body row">
                            <div class="card mt-3 shadow section-card">
                                <div class="card-header text-center fw-bold h4">Auto Images</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                            <div class="card shadow image-list-show">
                                                <div class="card-header text-center text-white">
                                                    <label class="text-center h4">Auto Image 1</label>
                                                </div>
                                                <div class="card-body">
                                                    <div class="auto-image-container mx-auto" id="auto-image-contaoners">
                                                        <input type="image" name="image_1" id="image_1" class="popimages img-fluid" src="">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                            <div class="card shadow image-list-show">
                                                <div class="card-header text-center text-white">
                                                    <label class="text-center h4">Auto Image 2</label>
                                                </div>
                                                <div class="card-body">
                                                    <div class="auto-image-container mx-auto" id="auto-image-contaoners">
                                                        <input type="image" name="image_2" id="image_2" class="popimages img-fluid" src="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                            <div class="card shadow image-list-show">
                                                <div class="card-header text-center text-white">
                                                    <label class="text-center h4">Auto Image 3</label>
                                                </div>
                                                <div class="card-body">
                                                    <div class="auto-image-container mx-auto" id="auto-image-contaoners">
                                                        <input type="image" name="image_3" id="image_3" class="popimages img-fluid" src="">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                            <div class="card shadow image-list-show">
                                                <div class="card-header text-center text-white">
                                                    <label class="text-center h4">Auto Image 4</label>
                                                </div>
                                                <div class="card-body">
                                                    <div class="auto-image-container mx-auto" id="auto-image-contaoners">
                                                        <input type="image" name="image_4" id="image_4" class="popimages img-fluid" src="">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                            <div class="card shadow image-list-show">
                                                <div class="card-header text-center text-white">
                                                    <label class="text-center h4">Auto Image 5</label>
                                                </div>
                                                <div class="card-body">
                                                    <div class="auto-image-container mx-auto" id="auto-image-contaoners">
                                                        <input type="image" name="image_5" id="image_5" class="popimages img-fluid" src="">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                            <div class="card shadow image-list-show">
                                                <div class="card-header text-center text-white">
                                                    <label class="text-center h4">Auto Image 6</label>
                                                </div>
                                                <div class="card-body">
                                                    <div class="auto-image-container mx-auto" id="auto-image-contaoners">
                                                        <input type="image" name="image_6" id="image_6" class="popimages img-fluid" src="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- end used auto model -->

<!-- new auto model -->
<div class="modal fade" id="newAutoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="card">
                        <div class="card-header text-center fw-bold h4">
                            <p id="modal-auto-id_1"></p>
                        </div>
                        <div class="card-body">
                            <div class="card-body row">
                                <div class="col-md-6">
                                    <label for="auto_brand_id">Auto Brand</label>
                                    <input type="text" id="brand_name1" name="brand_name1"
                                        class="form-control" readonly>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="specific_model">Model</label>
                                    <input type="text" id="specific_modal1" name="specific_modal1"
                                        class="form-control" readonly>
                                </div>

                                <div class="col-md-6 ">
                                    <label for="orp">On-Road Price</label>
                                    <input type="text" id="orp" name="orp"
                                        class="form-control" readonly>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="fuel_type_id">Fuel Type</label>
                                    <input type="text" id="fuel1" name="fuel1"
                                        class="form-control" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="passenger_capacity">Passenger Capacity</label>
                                    <input type="text" id="passenger_capacity"
                                        name="passenger_capacity" class="form-control" readonly>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="gear">Gear</label>
                                    <input type="text" id="gear" name="gear"
                                        class="form-control" readonly>
                                </div>
                                <div class="col-md-6 ">
                                    <label for="millage">Millage</label>
                                    <input type="text" id="millage" name="millage"
                                        class="form-control" readonly>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="engine_cc">Engine CC</label>
                                    <input type="text" id="engine_cc" name="engine_cc"
                                        class="form-control" readonly>
                                </div>
                                <div class="col-md-6 ">
                                    <label for="free_service">Free Service</label>
                                    <input type="text" id="free_service" name="free_service"
                                        class="form-control" readonly>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="finance_arrangements">Finance Arrangements</label>
                                    <input type="text" id="finance_arrangements"
                                        name="finance_arrangements" class="form-control" readonly>
                                </div>

                                <div class="col-md-6 ">
                                    <label for="vehicle_suitable">Vehicle Suitable For</label>
                                    <input type="text" id="vechicle_suitable1" name="vechicle_suitable1"
                                        class="form-control" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!---upload images --->
                    <div class="card mt-3 shadow section-card">
                        <div class="card-header text-center fw-bold h4">Auto Images</div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Image 1 -->
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <div class="card shadow image-list-show">
                                        <div class="card-header text-center text-white">
                                            <label class="text-center h4">Auto Image 1</label>
                                        </div>
                                        <div class="card-body">
                                            <div class="auto-image-container mx-auto">
                                                <input type="image" name="image_1" id="new_image_1" class="popimages img-fluid" src="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 2 -->
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <div class="card shadow image-list-show">
                                        <div class="card-header text-center text-white">
                                            <label class="text-center h4">Auto Image 2</label>
                                        </div>
                                        <div class="card-body">
                                            <div class="auto-image-container mx-auto">
                                                <input type="image" name="image_2" id="new_image_2" class="popimages img-fluid" src="">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image 3 -->
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <div class="card shadow image-list-show">
                                        <div class="card-header text-center text-white">
                                            <label class="text-center h4">Auto Image 3</label>
                                        </div>
                                        <div class="card-body">
                                            <div class="auto-image-container mx-auto">
                                                <input type="image" name="image_3" id="new_image_3" class="popimages img-fluid" src="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Image 4 -->
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <div class="card shadow image-list-show">
                                        <div class="card-header text-center text-white">
                                            <label class="text-center h4">Auto Image 4</label>
                                        </div>
                                        <div class="card-body">
                                            <div class="auto-image-container mx-auto">
                                                <input type="image" name="image_4" id="new_image_4" class="popimages img-fluid" src="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Image 5 -->
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <div class="card shadow image-list-show">
                                        <div class="card-header text-center text-white">
                                            <label class="text-center h4">Auto Image 5</label>
                                        </div>
                                        <div class="card-body">
                                            <div class="auto-image-container mx-auto">
                                                <input type="image" name="image_5" id="new_image_5" class="popimages img-fluid" src="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Image 6 -->
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <div class="card shadow image-list-show">
                                        <div class="card-header text-center text-white">
                                            <label class="text-center h4">Auto Image 6</label>
                                        </div>
                                        <div class="card-body">
                                            <div class="auto-image-container mx-auto">
                                                <input type="image" name="image_6" id="new_image_6" class="popimages img-fluid" src="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- end new auto model -->

<!-- auto status update model -->
<div class="modal fade auto_enquiry_status" id="auto_enquiry_status_update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" name="auto_enquiry_status_update" id="auto_enquiry_status_update">
                @csrf
                <div class="modal-header">
                    <h5>Auto Enquiry Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="table_id" name="table_id">
                    <small class="fw-medium">Enquiry Status</small>
                    <select name="auto_enquiry_status" id="auto_enquiry_status" class="form-select form-select-sm">
                        <option value="1">Pending</option>
                        <option value="2">Contacted</option>
                        <option value="3">Follow-up</option>
                        <option value="4">Reject</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- end auto status update model -->

@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function() {
        // used auto model data show in model
        $(document).on('click','.auto-details-btn', function() {
            $('#modal-auto-id').text("Auto Details : " + $(this).data('auto-id'));
            $('#brand_name').val($(this).data('brand'));
            $('#specific_modal').val($(this).data('model'));
            $('#registration_year').val($(this).data('year'));
            $('#reg_number').val($(this).data('reg-number'));
            $('#fuel_type').val($(this).data('fuel'));
            $('#km').val($(this).data('km'));
            $('#price').val($(this).data('price'));
            $('#owner').val($(this).data('owner'));
            $('#name').val($(this).data('name'));
            $('#chellan_status').val($(this).data('chellan_status'));
            $('#accident_status').val($(this).data('accident_status'));
            $('#rc_status').val($(this).data('rc_status'));
            $('#noc_status').val($(this).data('noc_status'));
            $('#loan_status').val($(this).data('loan_status'));
            $('#fc_status').val($(this).data('fc_status'));
            $('#permit_status').val($(this).data('permit_status'));
            $('#insurance').val($(this).data('insurance'));
            $('#rto').val($(this).data('rto'));
            $('#condition').val($(this).data('condition'));
            $('#loan_amount').val($(this).data('loan_amount'));
            $('#first_payment').val($(this).data('first_payment'));
            $('#emi_amount').val($(this).data('emi_amount'));
            $('#no_of_months').val($(this).data('no_of_months'));
            let financialAvailability = $(this).data('financial_availability');
            let isChecked = financialAvailability === "true" || financialAvailability === true;

            $('#financial_availability').prop('checked', isChecked);

            // Update hidden input when checkbox is toggled
            $('#financial_availability').on('change', function() {
                $('#financial_availability_hidden').val(this.checked ? "true" : "false");
            });

            for (let i = 1; i <= 6; i++) {
                let imageData = $(this).data(`image_${i}`);
                let imageElement = $(`#image_${i}`);

                if (imageData === 'Image Not Available') {
                    imageElement.replaceWith(
                        `<span class="text-muted fs-4" id="image_${i}_text">Image Not Available</span>`);
                } else {
                    imageElement.attr('src', imageData);
                }
            }
        });
        // end used auto model data show in model

        // new auto model data show in model
        $(document).on('click','.new-auto-details-btn' ,function() {
            $('#modal-auto-id_1').text("Auto Details :" +$(this).data('auto-id'));
            $('#brand_name1').val($(this).data('brand1'));
            $('#specific_modal1').val($(this).data('model1'));
            $('#orp').val($(this).data('orp'));
            $('#fuel1').val($(this).data('fuel1'));
            $('#passenger_capacity').val($(this).data('passenger'));
            $('#gear').val($(this).data('gear'));
            $('#millage').val($(this).data('millage'));
            $('#engine_cc').val($(this).data('engine'));
            $('#free_service').val($(this).data('free-service'));
            $('#finance_arrangements').val($(this).data('finance'));
            $('#vechicle_suitable1').val($(this).data('vechicle1'));
            // console.log($(this).data('image_1'));

            for (let i = 1; i <= 6; i++) {
                let imageData = $(this).data(`image_${i}`);
                let imageElement = $(`#new_image_${i}`);
                console.log(imageData);

                if (imageData === 'Image Not Available') {
                    console.log("if")
                    imageElement.replaceWith(
                        `<span class="text-muted" id="image_${i}_text">Image Not Available</span>`);
                } else {
                    console.log(imageElement)
                    imageElement.attr('src', imageData);
                }
            }
        });
        // end new auto model data show in model


        //update enquiry status
        $(document).on('click','.status_update_btn' ,function() {
            // let id = $(this).data('id');
            $('#table_id').val('')
            let id = $(this).data('id')
            $('#table_id').val(id)
            $('.auto_enquiry_status').modal('show');
        });

        $("#auto_enquiry_status_update").on("submit", function (e) {
            e.preventDefault(); // 🔴 Prevent normal page reload
            let id      = $('#table_id').val()
            let status  = $('#auto_enquiry_status').val()
            $.ajax({
                type:'POST',
                url:"{{route('enquiry-auto.status-update')}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    id:id,
                    status:status,
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
                            title: 'Enquiry Status!',
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
        });
    });

</script>
@endpush
