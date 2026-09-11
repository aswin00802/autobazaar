@extends('admin.layouts.app')
@section('title')
Users Post Auto List
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">

    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <h5 class="card-header">User Post Auto List</h5>
            <div class="card-body">
                <table class="datatables-fixed2 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Auto Unique ID</th>
                            <th>Auto Image</th>
                            <th>Brand</th>                                   
                            <th>Model</th>
                            <th>Auto Status</th>
                            <th>More Details</th>
                            <th>Actions</th>
                            <th>Fuel Type</th>                                    
                            <th>Reg.Year</th>
                            <th>Reg.Number</th>
                            <th>Kilometers</th>
                            <th>Price</th>
                            <th>Post Date</th>     
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($used_autos))
                            @foreach($used_autos as $auto)
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
                                        @if($auto->status == 0)
                                            <span class="badge rounded-pill text-bg-danger">Sold</span>
                                        @elseif($auto->status == 1)
                                            <span class="badge rounded-pill text-bg-success">Active</span>
                                        @elseif($auto->status == 2)
                                            <span class="badge rounded-pill text-bg-warning">Pending</span>
                                        @elseif($auto->status == 3)
                                            <span class="badge rounded-pill text-bg-danger">Reject</span>
                                        @endif
                                    </td>
                                    <td>
                                        @can('view_details_used_auto')
                                            <a href="{{ route('auto-management.used-auto.view-details',Crypt::encryptString($auto->id)) }}" class="btn btn-xs btn-twitter waves-effect waves-light text-white"><i class="icon-base ri ri-eye-line icon-16px me-2"></i>More</a>
                                        @endcan
                                    </td>
                                    <td>
                                         @can('edit_used_auto')
                                            <a href="{{ route('auto-management.used-auto.edit',$auto->id) }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                        @endcan
                                        @can('delete_used_auto')
                                            <a class="btn btn-sm btn-text-danger text-danger rounded-pill btn-icon item-delete" data-id="{{$auto->id}}"><i class="icon-base ri ri-delete-bin-2-line icon-20px"></i></a>
                                        @endcan
                                    </td>
                                    <td>{{$auto->autoFueltype->name??'' }}</td>
                                    <td>{{$auto->registration_year}}</td>
                                    <td>{{$auto->registration_number}}</td>
                                    <td>{{$auto->kilometer}}</td>
                                    <td>{{$auto->price_expectations}}</td>
                                    <td>{{date('d-m-Y',strtotime($auto->created_at))}}</td>
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