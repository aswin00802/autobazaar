<!doctype html>

<html lang="en" class="layout-wide customizer-hide" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Verify Email</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('admin/logo/logo.png')}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{asset('admin/assets/vendor/fonts/iconify-icons.css')}}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css -->

    <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/node-waves/node-waves.css')}}" />

    <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/core.css')}}" />
    <link rel="stylesheet" href="{{asset('admin/assets/css/demo.css')}}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <!-- endbuild -->

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/pages/page-auth.css')}}" />

    <!-- Helpers -->
    <!-- <script src="../../assets/vendor/js/helpers.js"></script> -->
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file. -->

    <!-- <script src="../../assets/js/config.js"></script> -->
  </head>

  <body>
    <!-- Content -->

    <div class="position-relative">
        <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
            <div class="authentication-inner py-6">
                <div class="card p-md-7 p-1">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mt-5">
                    <a href="" class="app-brand-link gap-2">
                        <img src="{{asset('admin/logo/logo.png')}}" class="img-fluid w-25">
                        <span class="app-brand-text demo text-heading fw-semibold">Ziga Infotech</span>
                    </a>
                    </div>
                    <!-- /Logo -->

                    <!-- Verify Email -->
                    <div class="card-body mt-1">
                        <h4 class="mb-1">Verify Your Email Address ✉️</h4>
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif
                        <p class="text-start mb-0">
                            Before proceeding, please check your email for a verification link.: <span class="h6">hello@example.com</span> If you did not receive the email.
                        </p>
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100 my-5">{{ __('click here to request another') }}</button>.
                        </form>
                        <!-- <a class="btn btn-primary w-100 my-5" href=""> Skip for now </a> -->
                        <p class="text-center mb-0">
                            Didn't get the mail?
                            <a href="javascript:void(0);"> Resend </a>
                        </p>
                    </div>
                </div>
                <img alt="mask" src="{{asset('admin/assets/img/illustrations/auth-basic-login-mask-light.png')}}" class="authentication-image d-none d-lg-block" />
                <!-- /Verify Email -->
            </div>
        </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->

    <!-- build:js assets/vendor/js/theme.js  -->

    <script src="{{asset('admin/assets/vendor/libs/jquery/jquery.js')}}"></script>

    <script src="{{asset('admin/assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{asset('admin/assets/vendor/js/bootstrap.js')}}"></script>
    <script src="{{asset('admin/assets/vendor/libs/node-waves/node-waves.js')}}"></script>

    <script src="{{asset('admin/assets/vendor/libs/@algolia/autocomplete-js.js')}}"></script>

    <script src="{{asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

    <script src="{{asset('admin/assets/vendor/libs/hammer/hammer.js')}}"></script>

    <script src="{{asset('admin/assets/vendor/libs/i18n/i18n.js')}}"></script>

    <script src="{{asset('admin/assets/vendor/js/menu.js')}}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->

    <!-- <script src="{{asset('admin/assets/js/main.js')}}"></script> -->

    <!-- Page JS -->
  </body>
</html>
