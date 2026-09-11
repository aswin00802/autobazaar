<!doctype html>

<html lang="en" class="layout-wide customizer-hide" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Reset Password</title>

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
                <div class="card p-md-7 p-1">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mt-5 mb-1">
                        <a href="#" class="app-brand-link gap-2">
                            <img src="{{asset('admin/logo/logo.png')}}" class="img-fluid w-25">
                            <span class="app-brand-text demo text-heading fw-semibold">Ziga Infotech</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <!-- Reset Password -->
                    <div class="card-body">
                        <h4 class="mb-1">Reset Password 🔒</h4>
                        <p class="mb-5">Your new password must be different from previously used passwords</p>
                        <form id="formAuthentication" class="mb-5" method="POST" action="{{ route('password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-floating form-floating-outline mb-5 form-control-validation">
                                <input type="text" class="form-control @error('email') is-invalid @enderror" value="{{ $email ?? old('email') }}" id="email" name="email" placeholder="Enter your email" autocomplete="email" autofocus />
                                <label for="email">Email</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-5 form-password-toggle form-control-validation">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" autocomplete="new-password" />
                                        <label for="password">New Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer"><i class="icon-base ri ri-eye-off-line icon-20px"></i></span>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-5 form-password-toggle form-control-validation">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input type="password" id="password_confirmation" class="form-control" name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" autocomplete="new-password"  />
                                        <label for="password_confirmation">Confirm Password</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer"><i class="icon-base ri ri-eye-off-line icon-20px"></i></span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary d-grid w-100 mb-5">Set new password</button>
                        </form>
                            <div class="text-center">
                                <a href="{{route('login')}}" class="d-flex align-items-center justify-content-center">
                                    <i class="icon-base ri ri-arrow-left-s-line scaleX-n1-rtl icon-20px me-1_5"></i>
                                    Back to login
                                </a>
                            </div>

                    </div>
                </div>
                <!-- /Reset Password -->
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
