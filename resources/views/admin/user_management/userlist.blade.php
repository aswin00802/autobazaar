@extends('admin.layouts.app')
@section('title')
Users List
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3">
        <div class="card">
            <h5 class="card-header">Admin Details</h5>
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr class="text-nowrap">
                            <th>Sl.no</th> 
                            <th>User Type</th>  
                            <th>Name</th> 
                            <th>Email</th> 
                            <th>Phone</th> 
                            <th>Reg. Date</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <tr>
                            <td>1</td>
                            <td>
                                <span class="badge rounded-pill text-bg-success">Admin User</span>
                            </td>
                            <td>{{ $admin->name ??'Not Available'}}</td>
                            <td>{{ $admin->email ??'Not Available'}}</td>
                            <td>{{ $admin->phone_number ?? 'Not Available' }}</td>
                            <td>{{ $admin->created_at ?? 'Not Available' }}</td>
                            <td><a href="{{ route('user-management.users-info' ,  Crypt::encryptString($admin->id)) }}" class="btn btn-primary text-white p-2">More Info</a></td>
                        </tr>
                        
                        <tr>
                            <td>2</td>
                            <td>
                                <span class="badge rounded-pill text-bg-info">Playstore User</span>
                            </td>
                            <td>{{ $playstore_test->name ??'Not Available'}}</td>
                            <td>{{ $playstore_test->email ??'Not Available'}}</td>
                            <td>{{ $playstore_test->phone_number ?? 'Not Available' }}</td>
                            <td>{{ $playstore_test->created_at ?? 'Not Available' }}</td>
                            <td><a href="{{ route('user-management.users-info' ,  Crypt::encryptString($playstore_test->id)) }}" class="btn btn-primary text-white p-2">More Info</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <h5 class="card-header">Other User's Details</h5>
            <div class="card-body">
                <table class="datatables-fixed2 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>  
                            <th>Name</th> 
                            <th>Phone</th> 
                            <th>User Area</th>                          
                            <th>Reg. Date</th>
                            <th>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($users))
                            @foreach ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>                                      
                                    <td>{{ $user->name ?? 'Not Available'}}</td>
                                    <td>{{ $user->phone_number ?? 'Not Available' }}</td>
                                    <td class="{{ $user->autoAreas?->name ? '' : 'text-danger' }}">
                                        {{ $user->autoAreas?->name ?? 'Not Available' }}
                                    </td>
                                    <td>{{ $user->created_at->toDateString() }}</td>
                                    <td><a href="{{ route('user-management.users-info' , Crypt::encryptString($user->id)) }}" class="btn btn-primary text-white p-2">More Info</a></td>
                                    
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
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
@endpush