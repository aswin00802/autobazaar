@extends('admin.layouts.app')
@section('title')
Auto Management / Used Auto
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endpush

@section('content')
<div class="row">
    @can('add_used_auto')
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
            <a href="{{route('auto-management.used-auto.create')}}" class="btn create-new btn-primary">
                <span>
                    <span class="d-flex align-items-center">
                        <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                        <span class="d-none d-sm-inline-block">Add Used Auto</span>
                    </span>
                </span>
            </a>
        </div>
    @endcan
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Auto Unique ID</th>
                            <th>Auto Image</th>
                            <th>Brand</th>                                   
                            <th>Model</th>
                            <th>More Details</th>
                            <th>Sell Auto</th>
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
                                        @can('view_details_used_auto')
                                        <a href="{{ route('auto-management.used-auto.view-details',Crypt::encryptString($auto->id)) }}" class="btn btn-sm btn-text-primary rounded-pill btn-icon" title="View"><i class="icon-base ri ri-eye-line icon-16px me-2"></i>More</a>
                                        @endcan
                                    </td>
                                    <td>
                                        @can('sell_used_auto')
                                        <a class="btn rounded-pill btn-google-plus waves-effect waves-light text-white sell_auto" data-id="{{ $auto->id }}" data-unique_id = {{ $auto->auto_unique_id }}><i class="icon-base ri ri-hand-heart-line icon-16px me-2"></i>Sell</a>
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

<div class="modal fade" id="auto_sell_model" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('auto-management.used-auto.sell') }}" name="sell_form" id="sell_form" method="post">
            @csrf
            <input type="hidden" id="post_id" name="post_id" value="">
            <div class="modal-header">
                <h5 class="modal-title" id="backDropModalTitle">Auto <span id="auto_id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col mb-6 mt-2">
                    <div class="form-floating form-floating-outline">
                        <input type="text" required id="buyer_name" name="buyer_name" class="form-control" placeholder="Enter Buyer Name" />
                        <label for="buyer_name">Buyer Name</label>
                    </div>
                    </div>
                </div>
                <div class="row g-4">
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="date" required id="sold_date" name="date" class="form-control" />
                            <label for="sold_date">Sold Date</label>
                        </div>
                    </div>
                    <div class="col mb-2">
                        <div class="form-floating form-floating-outline">
                            <input type="text" required id="sold_amount" name="sold_amount" class="form-control" placeholder="Sold Amount" />
                            <label for="sold_amount">Sold Amount</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="submit" class="btn btn-primary">Sold</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
<script src="{{asset('admin/assets/js/ui-modals.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function() {

        $(document).on('click','.sell_auto',function(){
            let id = $(this).data('id')
            let unique_id = $(this).data('unique_id')
            $('#auto_id').text('')
            $('#post_id').val('')
            $('#auto_id').text(unique_id)
            $('#post_id').val(id)
            $('#auto_sell_model').modal('show');
        });
    
        $(document).on("click", ".item-delete", function() {
            let el = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to delete this Auto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                customClass: {
                confirmButton: 'btn btn-primary me-3 waves-effect waves-light',
                cancelButton: 'btn btn-outline-secondary waves-effect'
                },
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    let id = el.data('id');
                    
                    $.ajax({
                        type:'POST',
                        url:"{{route('auto-management.used-auto.delete')}}",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            id:id,
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
                                    title: 'Deleted!',
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
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Deleted!',
                    //     text: 'Your file has been deleted.',
                    //     customClass: {
                    //     confirmButton: 'btn btn-success waves-effect'
                    //     }
                    // });
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Cancelled',
                    text: 'Your email tempalte file is safe :)',
                    icon: 'error',
                    customClass: {
                    confirmButton: 'btn btn-success waves-effect'
                    }
                });
                }
            });
        })
        
    });
    
</script>
@endpush