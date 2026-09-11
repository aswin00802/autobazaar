@extends('admin.layouts.app')
@section('title')
    Dashboard
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
@endpush

@section('content')
<div class="row g-6 mb-6">
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Total User</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-2">{{ $totalUsers }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-primary rounded-3">
                        <div class="icon-base ri ri-group-line icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Active Users</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{ $activeUsers }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-danger rounded">
                            <div class="icon-base ri ri-user-add-line icon-26px scaleX-n1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Today Register Users</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{ $todayUsers->count() }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-success rounded-3">
                            <div class="icon-base ri ri-user-follow-line icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Current Monthly Users</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{ $monthlyUsers }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-warning rounded-3">
                            <div class="icon-base ri ri-user-search-line icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Total Active Autos</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-2">{{ $totalActiveAutos }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-primary rounded-3">
                            <div class="icon-base ri ri-truck-line icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Total New Autos</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{ $totalNewAutos }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-danger rounded">
                            <div class="icon-base ri ri-truck-line icon-26px scaleX-n1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Total Used Autos</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{ $totalUsedAutos }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-success rounded-3">
                            <div class="icon-base ri ri-truck-line icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Total Private Cargo Autos</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{ $totalPrivateCargoAutos }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-warning rounded-3">
                            <div class="icon-base ri ri-truck-line icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Total Bajaj ReFinance Autos</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-2">{{ $totalBajajRefinanceAutos }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-primary rounded-3">
                            <div class="icon-base ri ri-truck-line icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Total Autos</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{ $totalAutos }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-danger rounded">
                            <div class="icon-base ri ri-truck-line icon-26px scaleX-n1"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Total Enquiry</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{ $totalEnquirys }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-success rounded-3">
                            <div class="icon-base ri ri-user-search-fill icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Current Monthly Enquiry</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{ $monthlyEnquirys }}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-warning rounded-3">
                            <div class="icon-base ri ri-user-search-fill icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="me-1">
                        <p class="text-heading mb-1">Total Sold Autos</p>
                        <div class="d-flex align-items-center">
                            <h4 class="mb-1 me-1">{{$soldAutos}}</h4>
                        </div>
                    </div>
                    <div class="avatar">
                        <div class="avatar-initial bg-label-warning rounded-3">
                            <div class="icon-base ri ri-tools-fill icon-26px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row mt-5">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5>Today's Users Details</h5>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Mobile No</th>
                            <th>Create At</th>    
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($todayUsers))
                            @foreach($todayUsers as $todayUser)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$todayUser->name}}</td>
                                    <td>{{$todayUser->phone_number}}</td>
                                    <td>{{$todayUser->created_at}}</td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
@endpush
