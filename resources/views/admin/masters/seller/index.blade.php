@extends('admin.layouts.app')
@section('title')
Auto Seller
@endsection

@push('css')
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="row">
            @can('add_auto_seller')
            <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
                <a href="{{route('masters.auto-seller.create')}}" class="btn create-new btn-primary">
                    <span>
                        <span class="d-flex align-items-center">
                            <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                            <span class="d-none d-sm-inline-block">Add New Seller</span>
                        </span>
                    </span>
                </a>
            </div>
            @endcan
            <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-datatable text-nowrap">
                        <table class="datatables-basic table table-bordered table-responsive">
                            <thead>
                                <tr>
                                    <th>Sl.No</th>
                                    <th>Seller Name</th>
                                    <th>Seller Type</th>
                                    <th>Location</th>
                                    <th>Address</th>
                                    <th>Contact</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!empty($sellers))
                                    @foreach($sellers as $seller)
                                        <tr>
                                            <td>{{ $loop->iteration}}</td>
                                            <td>{{ $seller->seller_name ?? null }}</td>
                                            <td>{{ $seller->seller_type ?? null}}</td>
                                            <td>{{ $seller->location ?? null }}</td>
                                            <td>{{ $seller->address ?? null }}</td>
                                            <td>{{ $seller->contact ?? null}}</td>
                                            <td>
                                                @can('edit_auto_seller')
                                                    <a href="{{ route('masters.auto-seller.edit',$seller->id) }}" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                                @endcan
                                                @can('delete_auto_seller')
                                                    <a class="btn btn-sm btn-text-danger text-danger rounded-pill btn-icon item-delete" data-id="{{$seller->id}}"><i class="icon-base ri ri-delete-bin-2-line icon-20px"></i></a>
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
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click", ".item-delete", function() {
            let el = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to delete this Auto Seller!",
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
                        url:"{{route('masters.auto-seller.delete')}}",
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