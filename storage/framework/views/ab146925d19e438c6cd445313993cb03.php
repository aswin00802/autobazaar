<?php $__env->startSection('title'); ?>
Driver Request List
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/animate-css/animate.css')); ?>" />
<link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.css')); ?>" />
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-datatable text-nowrap">
                <table class="datatables-fixed1 table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th>Sl.no</th>
                            <th>Name</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($driverRequests)): ?>
                            <?php $__currentLoopData = $driverRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($driver->name ?? ''); ?></td>
                                    <td><?php echo e($driver->number ?? ''); ?></td>
                                    <td><?php echo e($driver->autoArea->name ?? 'N/A'); ?></td>
                                    <td><?php echo e($driver->updated_at->format('d-m-Y h:i A')); ?></td>
                                    <td>
                                        <?php if($driver->status == 0): ?>
                                            <span class="badge rounded-pill text-bg-warning">Requested</span>
                                        <?php elseif($driver->status == 1): ?>
                                            <span class="badge rounded-pill text-bg-success">Approved</span>
                                        <?php elseif($driver->status == 2): ?>
                                            <span class="badge rounded-pill text-bg-danger">Deleted</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_driver_approved')): ?>
                                                <?php if($driver->status == 0): ?>
                                                    <form action="<?php echo e(route('driver.request.approve', $driver->id)); ?>" method="POST" onsubmit="return confirm('Are you sure to approve this request?');">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('POST'); ?>
                                                        <button type="submit" class="btn btn-sm btn-primary">Approve</button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('auto_driver_delete')): ?>
                                                <form action="<?php echo e(route('driver.request.delete', $driver->id)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('POST'); ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
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



<!-- auto status update model -->
<div class="modal fade auto_enquiry_status" id="auto_enquiry_status_update_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" name="auto_enquiry_status_update" id="auto_enquiry_status_update">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5>Auto Enquiry Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="table_id" name="table_id">
                    <small class="fw-medium">Enquiry Status</small>
                    <select name="auto_enquiry_status" id="auto_enquiry_status" class="form-select form-select-sm">
                        <option value="requested">Requested</option>
                        <option value="acknowledged">Acknowledged</option>
                        <option value="in_progress">In Progress</option>
                        <option value="dispatched">Dispatched</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                        <option value="cancelled">Cancelled</option>
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
<!-- end auto status update model -->

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('admin/js/custom-datatable.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/vendor/libs/sweetalert2/sweetalert2.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/js/extended-ui-sweetalert2.js')); ?>"></script>
<script src="<?php echo e(asset('admin/assets/vendor/libs/block-ui/jquery.blockUI.min.js')); ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        //update enquiry status
        $(document).on('click','.status_update_btn' ,function() {
            // let id = $(this).data('id');
            $('#table_id').val('')
            let id = $(this).data('id')
            $('#table_id').val(id)
            $('.auto_enquiry_status').modal('show');
        });

        $("#auto_enquiry_status_update").on("submit", function (e) {
            e.preventDefault(); // 🔴 Prevent normal page reload
            let id      = $('#table_id').val()
            let status  = $('#auto_enquiry_status').val()
            $.ajax({
                type:'POST',
                url:"<?php echo e(route('emergency.request.update')); ?>",
                data: {
                    "_token": "<?php echo e(csrf_token()); ?>",
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
                            title: 'Emergency Status!',
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
        
    });
    
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\autobazaar\resources\views/admin/driver/index.blade.php ENDPATH**/ ?>