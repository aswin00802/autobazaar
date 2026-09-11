<?php $__env->startSection('title'); ?>
Payments Setting
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <h5 class="card-header">Payments Setting</h5>
            <div class="card-body">
                <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('settings.payment-settings.update')); ?>" name="payment_settings" id="payment_settings" class="row g-5" method="post" novalidate>
                    <?php echo csrf_field(); ?>

                    <div class="col-12">
                        <h6>1. Payment Gateways</h6>
                        <hr class="mt-0" />
                    </div>

                    <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $enabled = old('_token') ? old("{$key}_enabled") === 'on' : $gateway['enabled'];
                        ?>
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" data-class="<?php echo e($key); ?>" <?php if($enabled): ?> checked <?php endif; ?> id="<?php echo e($key); ?>_enabled" name="<?php echo e($key); ?>_enabled" value="on" />
                                <label class="form-check-label" for="<?php echo e($key); ?>_enabled">Enable <?php echo e($gateway['label']); ?></label>
                            </div>

                            <?php $__currentLoopData = $gateway['fields']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $input = "{$key}_{$field}"; ?>
                                <div class="form-floating form-floating-outline mb-4 <?php echo e($key); ?> d-none">
                                    <input type="text"
                                           class="form-control <?php $__errorArgs = [$input];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           name="<?php echo e($input); ?>"
                                           id="<?php echo e($input); ?>"
                                           value="<?php echo e(old($input, $gateway['values'][$field] ?? '')); ?>"
                                           placeholder="<?php echo e($meta['label']); ?>"
                                           data-required="<?php echo e($meta['required'] ? '1' : '0'); ?>" />
                                    <label for="<?php echo e($input); ?>"><?php echo e($meta['label']); ?><?php if(!$meta['required']): ?> <small class="text-muted">(optional)</small><?php endif; ?></label>
                                    <?php $__errorArgs = [$input];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <div class="col-12">
                        <hr class="mt-0" />
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script type="text/javascript">
    $(document).ready(function () {

        // Show/hide the gateway's fields and toggle their required state.
        $(".form-check-input").on("change", function () {
            let targetClass = $(this).data("class");
            let $fields = $("." + targetClass);
            if ($(this).is(":checked")) {
                $fields.removeClass('d-none').addClass('d-block');
                $fields.find('input[data-required="1"]').attr('required', true);
            } else {
                $fields.removeClass('d-block').addClass('d-none');
                $fields.find('input').removeAttr('required').removeClass('is-invalid');
                $fields.find('.invalid-feedback.js-error').remove();
            }
        });

        $(".form-check-input:checked").each(function () {
            $(this).trigger("change");
        });

        // Client-side check: enabled gateways must have their required fields filled.
        $("#payment_settings").on("submit", function (e) {
            let valid = true;
            $(this).find('.invalid-feedback.js-error').remove();

            $(this).find('input[required]').each(function () {
                let $input = $(this);
                if ($.trim($input.val()) === '') {
                    valid = false;
                    $input.addClass('is-invalid');
                    if (!$input.siblings('.invalid-feedback').length) {
                        $input.closest('.form-floating').append(
                            '<div class="invalid-feedback d-block js-error">' + $input.attr('placeholder') + ' is required.</div>'
                        );
                    }
                } else {
                    $input.removeClass('is-invalid');
                }
            });

            if (!valid) {
                e.preventDefault();
                $(this).find('.is-invalid').first().focus();
            }
        });

        $("#payment_settings").on("input", "input.is-invalid", function () {
            $(this).removeClass('is-invalid').siblings('.invalid-feedback.js-error').remove();
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\autobazaar\resources\views/admin/settings/payment-setting/index.blade.php ENDPATH**/ ?>