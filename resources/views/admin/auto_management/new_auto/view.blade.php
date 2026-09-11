@extends('admin.layouts.app')
@section('title')
Auto Management / View Auto Info
@endsection

@push('css')
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3">
        <a href="{{route('auto-management.new-auto')}}" class="btn btn-secondary" style="float:right">Back</a>
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
                    <h4 class="card-header text-center">New Auto Details - {{$auto->auto_unique_id??''}}</h4>
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
                                <h5>On-Road Price</h5>
                                <p>{{ $auto->orp }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Fuel Type</h5>
                                <p>{{ $auto->autoFuelType->name ?? 'N/A'}}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Passenger Capacity</h5>
                                <p>{{ $auto->passenger_capacity }}</p>
                            </div>

                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Gear</h5>
                                <p>{{ $auto->gear }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Millage</h5>
                                <p>{{ $auto->millage }}</p>
                            </div>

                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Engine CC</h5>
                                <p>{{ $auto->engine_cc}}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Free Service</h5>
                                <p>{{ $auto->free_service }}</p>
                            </div>

                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Finance Arrangements</h5>
                                <p>{{ $auto->finance_arrangements }}</p>
                            </div>
                            <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                <h5>Vehicle Suitable For</h5>
                                <p>{{ $auto->vehicle_suitable}}</p>
                            </div>
                            @if($auto->financial_availability == true)
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <h5>Loan Amount</h5>
                                    <p>{{ $auto->loan_amount}}</p>
                                </div>
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <h5>First Payment</h5>
                                    <p>{{ $auto->first_payment}}</p>
                                </div>
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <h5>EMI Amount</h5>
                                    <p>{{ $auto->emi_amount}}</p>
                                </div>
                                <div class="col-12 col-sm-4 col-md-4 col-lg-4">
                                    <h5>No Of Months</h5>
                                    <p>{{ $auto->no_of_months}}</p>
                                </div>
                            @endif
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
