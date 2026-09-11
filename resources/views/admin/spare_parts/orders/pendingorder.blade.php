@extends('admin.layouts.app')
@section('title')
Spare Parts / Pending Orders
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
            <h5 class="card-header">Pending Orders Details</h5>
            <div class="card-body">
                <table class="datatables-fixed2 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>  
                            <th>Name</th> 
                            <th>Phone</th> 
                            <th>Product</th>      
                            <th>Brand</th>                    
                            <th>Model</th>
                            <th>Quantity</th>
                            <th>More Details</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($orders))
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>                                      
                                    <td>{{ $order->user->name ?? 'Not Available'}}</td>
                                    <td>{{ $order->user->phone_number ?? 'Not Available' }}</td>
                                   <td>{{ $order->product->product->name ?? 'N/A' }}</td>
                                    <td>{{ $order->product->autoBrands->brand_name ?? 'N/A' }}</td>
                                    <td>{{ $order->product->autoModel->model_name ?? 'N/A' }}</td>
                                    <td>{{ $order->qnty }}</td>
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-info view-more"
                                            data-id="{{ $order->product_id }}">
                                            <i class="icon-base ri ri-eye-line icon-16px me-2"></i> View
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->order_status === 'pending' ? 'warning' : ($order->order_status === 'shipped' ? 'info' : 'secondary') }}">
                                            {{ ucfirst($order->order_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="demo-inline-spacing">
                                            <button type="button" class="btn rounded-pill btn-icon btn-primary waves-effect waves-light order_spareparts_status_btn" data-id ="{{$order->id}}" data-statusName="{{$order->order_status}}">
                                                <span class="icon-base ri ri-file-edit-line icon-15px text-white"></span>
                                            </button>
                                            @can('sparepart_deleteorders')
                                            <button type="button" class="btn rounded-pill btn-icon btn-danger waves-effect waves-light delete_btn" data-id ="{{$order->id}}" data-statusName="{{$order->order_status}}">
                                                <span class="icon-base ri ri-delete-bin-line icon-15px text-white"></span>
                                            </button>
                                            @endcan
                                        </div>
                                        <!-- <button type="button" class="btn btn-primary order_spareparts_status_btn" data-id ="{{$order->id}}" data-statusName="{{$order->order_status}}"><i class="icon-base ri ri-file-edit-line icon-20px"></i></button> -->
                                        
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


<!-- model -->
<div class="modal fade" id="productDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Product Details</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="productDetailsBody">
                <div class="text-center">
                    <span class="spinner-border"></span>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade order_spareparts_status" id="order_spareparts_status_update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" name="order_spareparts_status_update" id="order_spareparts_status_update">
                @csrf
                <div class="modal-header">
                    <h5>Spareparts Orders Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="table_id" name="table_id">
                    <small class="fw-medium">Order Status</small>
                    <select name="order_spareparts_status" id="order_spareparts_status" class="form-select form-select-sm">
                        <option value="pending">Pending</option>
                        <option value="shipped">Shipped</option>
                        <option value="Contacted">contacted</option>
                        <option value="cancel">Cancel</option>
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
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
<script>
$(document).on('click', '.view-more', function () {

    let productId = $(this).data('id');
    $('#productDetailsModal').modal('show');
    $('#productDetailsBody').html('<div class="text-center"><span class="spinner-border"></span></div>');

    $.get("{{ route('spare-parts.product.details') }}/" + productId, function (res) {

        let html = `
        <div class="row">
            <div class="col-md-4">
                <img src="/${res.product?.image ?? ''}" class="img-fluid rounded">
            </div>

            <div class="col-md-8">
                <h4>${res.product?.name ?? ''}</h4>
                <p><b>Category:</b> ${res.product?.category?.name ?? ''}</p>
                <p><b>Sub Category:</b> ${res.product?.sub_category?.name ?? ''}</p>
                <p><b>Description:</b> ${res.product?.description ?? '-'}</p>
            </div>
        </div>

        <hr>
        <h5>Brand & Model Price</h5>

        <div class="card mb-2">
            <div class="card-body">
                <p><b>Brand:</b> ${res.auto_brands?.brand_name ?? ''}</p>
                <p><b>Model:</b> ${res.auto_model?.model_name ?? ''}</p>
                <p><b>Price:</b> ₹${res.price}</p>
                <p><b>Offer Price:</b> ₹${res.offer_price ?? '-'}</p>

                <div class="d-flex gap-2">
        `;

        if (Array.isArray(res.images)) {
            res.images.forEach(img => {
                html += `<img src="/${img.image}" width="70" class="rounded">`;
            });
        }

        html += `
                </div>
            </div>
        </div>
        `;

        $('#productDetailsBody').html(html);
    });
});

$(document).on('click','.order_spareparts_status_btn' ,function() {
    // let id = $(this).data('id');
    $('#table_id').val('')
    $('#order_spareparts_status').val('')
    let id = $(this).data('id')
    let status = $(this).data('statusname')
    $('#table_id').val(id)
    $('#order_spareparts_status').val(status)
    $('.order_spareparts_status').modal('show');
});

$("#order_spareparts_status_update").on("submit", function (e) {
    e.preventDefault(); // 🔴 Prevent normal page reload
    let id      = $('#table_id').val()
    let status  = $('#order_spareparts_status').val()
    $.ajax({
        type:'POST',
        url:"{{route('spare-parts.orders.status-update')}}",
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
                    title: 'Order Status!',
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


$(document).on("click", ".delete_btn", function() {
    let el = $(this);
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to delete this order!",
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
                url:"{{route('spare-parts.orders.delete')}}",
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
        } else if (result.dismiss === Swal.DismissReason.cancel) {
        Swal.fire({
            title: 'Cancelled',
            text: 'Your order is safe :)',
            icon: 'error',
            customClass: {
            confirmButton: 'btn btn-success waves-effect'
            }
        });
        }
    });
})
</script>
@endpush