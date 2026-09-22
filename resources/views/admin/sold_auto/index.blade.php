@extends('admin.layouts.app')
@section('title')
Sold Auto List
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
                <a href="" class="btn create-new btn-primary">
                    <span>
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-list-check icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">View Active Autos</span>
                        </span>
                    </span>
                </a>
            </div>
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-datatable text-nowrap">
                        <table class="datatables-basic table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Sl.no</th>
                                    <th>Auto ID</th>                                  
                                    <th>Buyer Name</th> 
                                    <th>Date</th>
                                    <th>Auto Price</th> 
                                    <th>Sold Amount</th> 
                                    <!-- <th>Action</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($soldAutos))
                                    @foreach($soldAutos as $soldauto)
                                        <tr>
                                            <td>{{$loop->iteration}}</td>
                                            <td>{{ $soldauto->auto->auto_unique_id ?? '' }}</td>
                                            <td>{{$soldauto->buyer_name}}</td>
                                            <td>{{ date('d-m-Y',strtotime($soldauto->date)) }}</td>
                                            <td>{{ $soldauto->auto->price_expectations ?? '' }}</td>
                                            <td>{{$soldauto->sold_amount}}</td>
                                            <!-- <td>
                                                <a class="btn btn-sm btn-text-danger text-danger rounded-pill btn-icon item-delete" data-id="{{$soldauto->id}}"><i class="icon-base ri ri-delete-bin-2-line icon-20px"></i></a>
                                            </td> -->
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
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
@endpush