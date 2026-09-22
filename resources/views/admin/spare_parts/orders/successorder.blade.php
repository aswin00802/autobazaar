@extends('admin.layouts.app')
@section('title')
Spare Parts / Success Orders
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
            <h5 class="card-header">Success Orders Details</h5>
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
                                        <span class="badge rounded-pill text-bg-warning">
                                            {{ ucfirst($order->order_status) }}
                                        </span>
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
@endsection

@push('scripts')
<script src="{{asset('admin/js/custom-datatable.js')}}"></script>
<script src="{{asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('admin/assets/js/extended-ui-sweetalert2.js')}}"></script>
<script src="{{ asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js') }}"></script>
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
</script>
@endpush