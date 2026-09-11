@extends('admin.layouts.app')
@section('title')
User Info
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3">
        <a href="{{route('user-management.users-list')}}" class="btn btn-secondary" style="float:right">Back</a>
    </div>
    <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
        <div class="card mb-6">
            <div class="card-body pt-12">
                <div class="user-avatar-section">
                    <div class="d-flex align-items-center flex-column">
                        <img class="img-fluid rounded-3 mb-4" src="{{ $user->userInfo && $user->userInfo->profile ? asset($user->userInfo->profile) : url('/assets/images/avatars/avatar-2.png') }}" height="120" width="120" alt="User avatar" />
                        <div class="user-info text-center">
                            <h5>{{$user->name}}</h5>
                            @if($user->status == 1)
                                <span class="badge bg-label-success rounded-pill">Active</span>
                            @else
                                <span class="badge bg-label-danger rounded-pill">Deactive</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-around flex-wrap my-6 gap-0 gap-md-3 gap-lg-4">
                    <div class="d-flex align-items-center me-5 gap-4">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="icon-base ri ri-mail-line icon-24px"></i>
                            </div>
                        </div>
                        <div>
                            <span>{{ $user->email }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="icon-base ri ri-phone-fill icon-24px"></i>
                            </div>
                        </div>
                        <div>
                            <span>{{ $user->phone_number }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <h5 class="card-header">Post Autos</h5>
                    <div class="card-body">
                        <table class="datatables-fixed2 table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Sl.no</th>  
                                    <th>Brand Name</th> 
                                    <th>Model Name</th> 
                                    <th>Auto Unique ID</th>                          
                                    <th>Post Date</th>
                                    <th>Auto Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($autos))
                                    @foreach($autos as $auto)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $auto->autoBrands->brand_name??'' }}</td>
                                            <td>{{ $auto->autoModel->model_name??'' }}</td>
                                            <td>
                                                @if(isset($auto->auto_unique_id))
                                                    @if($auto->auto_usage_status == 'new_auto')
                                                        @php
                                                            $view_route = route('auto-management.new-auto.view-details',Crypt::encryptString($auto->id));
                                                        @endphp
                                                    @elseif($auto->auto_usage_status == 'used_auto')
                                                        @php
                                                            $view_route = route('auto-management.used-auto.view-details',Crypt::encryptString($auto->id));
                                                        @endphp
                                                    @elseif($auto->auto_usage_status == 'private_cargo')
                                                        @php
                                                            $view_route = route('auto-management.private-cargo-auto.view-details',Crypt::encryptString($auto->id));
                                                        @endphp
                                                    @elseif($auto->auto_usage_status == 'bajaj_refinance')
                                                        @php
                                                            $view_route = route('auto-management.bajaj-refinance-auto.view-details',Crypt::encryptString($auto->id));
                                                        @endphp
                                                    @endif
                                                    <a href="{{$view_route}}" class="btn btn-xs btn-twitter waves-effect waves-light text-white"><i class="icon-base ri ri-eye-line icon-16px me-2"></i>{{ $auto->auto_unique_id }}</a>
                                                @endif
                                            </td>
                                            <td>{{ $auto->created_at }}</td>
                                            <td>{{ $auto->auto_status }}</td>
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
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/modal-edit-user.js')}}"></script>
<script src="{{asset('admin/assets/js/app-user-view.js')}}"></script>
<script src="{{asset('admin/assets/js/app-user-view-account.js')}}"></script>
@endpush