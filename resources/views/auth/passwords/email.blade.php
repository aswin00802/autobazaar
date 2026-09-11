<!doctype html>

<html lang="en" class="layout-wide customizer-hide" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewpor content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Forgot Password</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('admin/logo/logo.png')}}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <link rel="stylesheet" href="{{asset('admin/assets/vendor/fonts/iconify-icons.css')}}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css -->

    <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/node-waves/node-waves.css')}}" />

    <link rel="stylesheet" href="{{asset('admin/assets/vendor/css/core.css')}}" />
    <link rel="stylesheet" href="{{asset('admin/assets/css/demo.css')}}" />

    <!-- Vendors CSS -->

    <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />

    <!-- endbuild -->

    <!-- Vendor -->
    <link rel="stylesheet" href="{{asset('admin/assets/vendor/libs/@form-validation/form-validation.css')}}" />

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
                <!-- Logo -->
                <div class="card p-md-7 p-1">
                    <!-- Forgot Password -->
                    <div class="app-brand justify-content-center mt-5">
                        <a href="#" class="app-brand-link gap-2">
                            <img src="{{asset('admin/logo/logo.png')}}" class="img-fluid w-25">
                            <span class="app-brand-text demo text-heading fw-semibold">Materialize</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <div class="card-body mt-1">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <h4 class="mb-1">Forgot Password? 🔒</h4>
                        <p class="mb-5">Enter your email and we'll send you instructions to reset your password</p>
                        <form id="formAuthentication" class="mb-5" method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="form-floating form-floating-outline mb-5 form-control-validation">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" id="email" name="email" placeholder="Enter your email" autofocus />
                                <label>Email</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <button class="btn btn-primary d-grid w-100 mb-5">Send Password Reset Link</button>
                        </form>
                        <div class="text-center">
                            <a href="{{route('login')}}" class="d-flex align-items-center justify-content-center">
                                <i class="icon-base ri ri-arrow-left-s-line scaleX-n1-rtl icon-20px me-1_5"></i>
                                Back to login
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Forgot Password -->
                <img alt="mask" src="{{asset('admin/assets/img/illustrations/auth-basic-login-mask-light.png')}}" class="authentication-image d-none d-lg-block" />
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
    <!-- <script src="{{asset('admin/assets/vendor/libs/@form-validation/popular.js')}}"></script>
    <script src="{{asset('admin/assets/vendor/libs/@form-validation/bootstrap5.js')}}"></script>
    <script src="{{asset('admin/assets/vendor/libs/@form-validation/auto-focus.js')}}"></script> -->

    <!-- Main JS -->

    <script src="{{asset('admin/assets/js/main.js')}}"></script>

    <!-- Page JS -->
    <script src="{{asset('admin/assets/js/pages-auth.js')}}"></script>
  </body>
</html>
