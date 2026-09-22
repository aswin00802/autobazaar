<?php $__env->startSection('title'); ?>
Spare Parts / Products
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/animate-css/animate.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')); ?>" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="row mt-5">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('add_sparepart_product')): ?>
        <div class="col-12 col-sm-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-end">
            <a href="<?php echo e(route('spare-parts.product.create')); ?>" class="btn create-new btn-primary">
                <span>
                    <span class="d-flex align-items-center">
                        <i class="icon-base ri ri-add-line icon-18px me-sm-1"></i>
                        <span class="d-none d-sm-inline-block">Add New Product</span>
                    </span>
                </span>
            </a>
        </div>
    <?php endif; ?>
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5>Spare Parts Products</h5>
            </div>
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Categories</th>
                            <th>Sub Categories</th>
                            <th>Name</th>
                            <th>More Details</th>
                            <th>Image</th>
                            <th>Action</th>    
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($products)): ?>
                            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($product->Category->name ?? ''); ?></td>
                                    <td><?php echo e($product->subCategory->name ?? ''); ?></td>
                                    <td><?php echo e($product->name); ?></td>
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-info view-more"
                                            data-id="<?php echo e($product->id); ?>">
                                            <i class="icon-base ri ri-eye-line icon-16px me-2"></i> View
                                        </button>
                                    </td>
                                    <td>
                                        <?php if(isset($product->image)): ?>
                                            <img src="<?php echo e(asset($product->image)); ?>" alt="image" class="img-fluid" style="width:80px;height:80px">
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit_sparepart_product')): ?>
                                            <a href="<?php echo e(route('spare-parts.product.edit',$product->id)); ?>" class="btn btn-sm btn-text-secondary rounded-pill btn-icon item-edit"><i class="icon-base ri ri-edit-box-line icon-20px"></i></a>
                                        <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete_sparepart_product')): ?>
                                            <a class="btn btn-sm btn-text-danger text-danger rounded-pill btn-icon item-delete" data-id="<?php echo e($product->id); ?>"><i class="icon-base ri ri-delete-bin-2-line icon-20px"></i></a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
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

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('admin/js/custom-datatable.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/extended-ui-sweetalert2.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js')); ?>"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $(document).on("click", ".item-delete", function() {
            let el = $(this);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to delete this Spare parts Product!",
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
                        url:"<?php echo e(route('spare-parts.product.delete')); ?>",
                        data: {
                            "_token": "<?php echo e(csrf_token()); ?>",
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

<script>
$(document).on('click', '.view-more', function () {

    let productId = $(this).data('id');
    $('#productDetailsModal').modal('show');
    $('#productDetailsBody').html('<div class="text-center"><span class="spinner-border"></span></div>');

    $.get("<?php echo e(route('spare-parts.product.details')); ?>/" + productId , function (res) {

        let html = `
        <div class="row">
            <div class="col-md-4">
                <img src="/${res.image}" class="img-fluid rounded">
            </div>

            <div class="col-md-8">
                <h4>${res.name}</h4>
                <p><b>Category:</b> ${res.category?.name ?? ''}</p>
                <p><b>Sub Category:</b> ${res.sub_category?.name ?? ''}</p>
                <p><b>Description:</b> ${res.description ?? '-'}</p>
            </div>
        </div>
        <hr>
        <h5>Brand & Model Prices</h5>
        `;

        res.product_brand_model.forEach(item => {

            html += `
            <div class="card mb-2">
                <div class="card-body">
                    <p><b>Brand:</b> ${item.auto_brands?.brand_name ?? ''}</p>
                    <p><b>Model:</b> ${item.auto_model?.model_name ?? ''}</p>
                    <p><b>Price:</b> ₹${item.price}</p>
                    <p><b>Offer Price:</b> ₹${item.offer_price ?? '-'}</p>

                    <div class="d-flex gap-2">
            `;

            item.images.forEach(img => {
                html += `<img src="/${img.image}" width="70" class="rounded">`;
            });

            html += `
                    </div>
                </div>
            </div>
            `;
        });

        $('#productDetailsBody').html(html);
    });
});
</script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\autobazaar\resources\views/admin/spare_parts/products/index.blade.php ENDPATH**/ ?>