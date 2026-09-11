@extends('admin.layouts.app')
@section('title')
Auto Management / View Bajaj ReFinance Auto Info
@endsection

@push('css')
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3">
        <a href="{{route('auto-management.bajaj-refinance-auto')}}" class="btn btn-secondary" style="float:right">Back</a>
    </div>
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <div class="card mb-6">
            <h4 class="card-header text-center">Auto Images</h4>
            <div class="card-body pt-12">
                <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @if(isset($auto->image_1))
                            <div class="carousel-item active">
                                <img class="d-block w-100" src="{{ asset($auto->image_1) }}" alt="First slide" />
                            </div>
                        @endif
                        @if(isset($auto->image_2))
                            <div class="carousel-item">
                                <img class="d-block w-100" src="{{ asset($auto->image_2) }}" alt="Second slide" />
                            </div>
                        @endif
                        @if(isset($auto->image_3))
                            <div class="carousel-item">
                                <img class="d-block w-100" src="{{ asset($auto->image_3) }}" alt="Third slide" />
                            </div>
                        @endif
                        @if(isset($auto->image_4))
                            <div class="carousel-item active">
                                <img class="d-block w-100" src="{{ asset($auto->image_4) }}" alt="First slide" />
                            </div>
                        @endif
                        @if(isset($auto->image_5))
                            <div class="carousel-item">
                                <img class="d-block w-100" src="{{ asset($auto->image_5) }}" alt="Second slide" />
                            </div>
                        @endif
                        @if(isset($auto->image_6))
                            <div class="carousel-item">
                                <img class="d-block w-100" src="{{ asset($auto->image_6) }}" alt="Third slide" />
                            </div>
                        @endif
                    </div>
                    <a class="carousel-control-prev" href="#carouselExample" role="button" data-bs-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExample" role="button" data-bs-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                <div class="card">
                    <h4 class="card-header text-center">Bajaj ReFinance Auto Details - {{$auto->auto_unique_id??''}}</h4>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                <div class="card">
                    <h4 class="card-header text-center">Auto Details</h4>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Auto Brand</h5>
                                <p>{{ $auto->autoBrands->brand_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Auto Model</h5>
                                <p>{{ $auto->autoModel->model_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Registration Year</h5>
                                <p>{{ $auto->registration_year }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Registration Number</h5>
                                <p>{{ $auto->registration_number }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Fuel Type</h5>
                                <p>{{ $auto->autoFuelType->name ?? 'N/A'}}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Kilometer</h5>
                                <p>{{ $auto->kilometer . ' km' ?? 'N/A' }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Price</h5>
                                <p>{{ $auto->price_expectations ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                <div class="card">
                    <h4 class="card-header text-center">Auto Owner Details</h4>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Owner</h5>
                                <p>{{ $auto->autoOwners->name??'' }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Name</h5>
                                <p>{{$auto->name}}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Owner Mobile No</h5>
                                <p>{{$auto->mobile_number}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                <div class="card">
                    <h4 class="card-header text-center">Auto Status</h4>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Chellan Status</h5>
                                <p>{{ $auto->chellan_status }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Accident Status</h5>
                                <p>{{ $auto->accident_status }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>RC Status</h5>
                                <p>{{ $auto->rc_status }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>NOC Status</h5>
                                <p>{{ $auto->noc_status }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Loan Status</h5>
                                <p>{{ $auto->loan_status }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>FC Status</h5>
                                <p>{{ $auto->fc_status }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Permit Status</h5>
                                <p>{{ $auto->permit_status }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Insurance</h5>
                                <p>{{ $auto->insurance }}</p>
                            </div>
                             <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>RTO</h5>
                                <p>{{ $auto->rto }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Condition</h5>
                                <p>{{ $auto->condition }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-2">
                <div class="card">
                    <h4 class="card-header text-center">Financial Availability Status</h4>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Financial Availability</h5>
                                <p>
                                    @if($auto->financial_availability === "true" || $auto->financial_availability === 1)
                                        Active
                                    @else
                                    @endif
                                </p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Loan Amount</h5>
                                <p>{{ $auto->loan_amount }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Initial Payment</h5>
                                <p>{{ $auto->first_payment }}</p>
                            </div>
                             <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>EMI Amount</h5>
                                <p>{{ $auto->emi_amount }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>No. Of Months</h5>
                                <p>{{ $auto->no_of_months }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/assets/vendor/libs/swiper/swiper.js')}}"></script>
<script src="{{asset('admin/assets/js/ui-carousel.js')}}"></script>
@endpush