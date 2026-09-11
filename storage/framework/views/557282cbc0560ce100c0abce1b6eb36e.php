<!doctype html>

<html
  lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?php echo $__env->yieldContent('title'); ?></title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <?php if(getSetting('fav_icon')): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset(getSetting('fav_icon'))); ?>" />
    <?php else: ?>
        <link rel="icon" type="image/x-icon" href="<?php echo e(asset('admin/logo/logo.png')); ?>" />
    <?php endif; ?>
    <!-- <link rel="icon" type="image/x-icon" href="<?php echo e(asset('admin/assets/img/favicon/favicon.ico')); ?>" /> -->

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />

    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/fonts/iconify-icons.css')); ?>" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css -->

    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/node-waves/node-waves.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/css/core.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/css/demo.css')); ?>" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />

    <!-- endbuild -->

    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/swiper/swiper.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/libs/apex-charts/apex-charts.css')); ?>" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('admin/assets/vendor/css/pages/cards-statistics.css')); ?>" />

    <?php echo $__env->yieldPushContent('css'); ?>
    <style>
        .errors {
            color: red!important;

        }
        label .errors {
            font-size:12px!important;
        }
        .buttons-html5{
            margin:5px!important;
        }
    </style>
    <!-- Helpers -->
    <script src="<?php echo e(asset('admin/assets/vendor/js/helpers.js')); ?>"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file. -->

    <script src="<?php echo e(asset('admin/assets/js/config.js')); ?>"></script>
  </head>

  <body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- side nav -->
            <?php echo $__env->make('admin.include.sidenav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!-- End side nav -->
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                 <?php echo $__env->make('admin.include.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <!-- End Navbar -->
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- print success session message -->
                        <?php if(session()->has('success')): ?>
                            <div class="alert alert-success alert-dismissible" role="alert">
                                <strong><?php echo e(session('success')); ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <!-- print error session message -->
                        <?php if(session()->has('error')): ?>
                            <div class="alert alert-danger alert-dismissible" role="alert">
                                <strong><?php echo e(session('error')); ?></strong>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                    <!-- End Content -->
                    <!-- Footer -->
                    <?php echo $__env->make('admin.include.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <!-- End Footer -->
                    <div class="content-backdrop fade"></div>
                </div>
                <!-- End Content wrapper -->
            </div>
            <!-- End Layout container -->
        </div>
        <div class="layout-overlay layout-menu-toggle"></div>
        <div class="drag-target"></div>
    </div>

    <!-- Core JS -->

    <!-- build:js assets/vendor/js/theme.js  -->

    <script src="<?php echo e(asset('admin/assets/vendor/libs/jquery/jquery.js')); ?>"></script>

    <script src="<?php echo e(asset('admin/assets/vendor/libs/popper/popper.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/js/bootstrap.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/libs/node-waves/node-waves.js')); ?>"></script>

    <script src="<?php echo e(asset('admin/assets/vendor/libs/@algolia/autocomplete-js.js')); ?>"></script>

    <script src="<?php echo e(asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>

    <script src="<?php echo e(asset('admin/assets/vendor/libs/hammer/hammer.js')); ?>"></script>

    <script src="<?php echo e(asset('admin/assets/vendor/libs/i18n/i18n.js')); ?>"></script>

    <script src="<?php echo e(asset('admin/assets/vendor/js/menu.js')); ?>"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="<?php echo e(asset('admin/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')); ?>"></script>
    <!-- <script src="<?php echo e(asset('admin/assets/vendor/libs/apex-charts/apexcharts.js')); ?>"></script>
    <script src="<?php echo e(asset('admin/assets/vendor/libs/cleave-zen/cleave-zen.js')); ?>"></script> -->

    <!-- Main JS -->

    <script src="<?php echo e(asset('admin/assets/js/main.js')); ?>"></script>

    <!-- Page JS -->
    <!-- <script src="<?php echo e(asset('admin/assets/js/dashboards-crm.js')); ?>"></script> -->

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <?php echo $__env->yieldContent('scripts'); ?>

  </body>
</html><?php /**PATH C:\xampp\htdocs\autobazaar\resources\views/admin/layouts/app.blade.php ENDPATH**/ ?>