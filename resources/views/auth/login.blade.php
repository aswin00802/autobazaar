
<!doctype html>

<html lang="en" class="layout-wide customizer-hide" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title>Login</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    @if(getSetting('fav_icon'))
        <link rel="icon" type="image/x-icon" href="{{ asset(getSetting('fav_icon')) }}" />
    @else
        <link rel="icon" type="image/x-icon" href="{{asset('admin/logo/logo.png')}}" />
    @endif
    <!-- <link rel="icon" type="image/x-icon" href="{{asset('admin/logo/logo.png')}}" /> -->

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
    <!-- <script src="{{asset('admin/assets/vendor/js/helpers.js')}}"></script> -->
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config: Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file. -->

    <!-- <script src="{{asset('admin/assets/js/config.js')}}"></script> -->
  </head>

  <body>
    <!-- Content -->

    <div class="position-relative">
        <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
            <div class="authentication-inner py-6">
                <!-- Login -->
                <div class="card p-md-7 p-1">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center mt-5">
                        <a href="#" class="app-brand-link gap-2">
                            @if(getSetting('web_logo'))
                                <img src="{{ asset(getSetting('web_logo')) }}" class="img-fluid w-25">
                            @else
                                <img src="{{asset('admin/logo/logo.png')}}" class="img-fluid w-25">
                            @endif
                            <!-- <img src="{{asset('admin/logo/logo.png')}}" class="img-fluid w-25"> -->
                            <span class="app-brand-text demo text-heading fw-semibold fs-3">{{ getSetting('business_name') }}</span>
                        </a>
                    </div>
                    <!-- /Logo -->

                    <div class="card-body mt-1">
                        <h4 class="mb-1">Welcome 👋</h4>
                        <p class="mb-5">Please sign-in to your account</p>
                        <form id="formAuthentication" class="mb-5" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="form-floating form-floating-outline mb-5 form-control-validation">
                                <input type="text" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" id="email" name="email" placeholder="Enter your email or username" autofocus />
                                <label for="email">Email or Username</label>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="mb-5">
                                <div class="form-password-toggle form-control-validation">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" />
                                            <label for="password">Password</label>
                                        </div>
                                        <span class="input-group-text cursor-pointer toggle-password"><i class="icon-base ri ri-eye-off-line icon-20px"></i></span>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <!-- <div class="mb-5 d-flex justify-content-between mt-5">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remember-me" />
                                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="float-end mb-1 mt-2">
                                        <span>Forgot Password?</span>
                                    </a>
                                @endif
                            </div> -->
                            <div class="mb-5">
                                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>

                        <p class="text-center mb-5">
                            @if (Route::has('register'))<span>New on our platform?</span><a href="{{route('register')}}"><span> Create an account</span></a>@endif
                        </p>

                        <!-- <div class="divider my-5">
                            <div class="divider-text">or</div>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-facebook">
                                <i class="icon-base ri ri-facebook-fill icon-18px"></i>
                            </a>

                            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-twitter">
                                <i class="icon-base ri ri-twitter-fill icon-18px"></i>
                            </a>

                            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-github">
                                <i class="icon-base ri ri-github-fill icon-18px"></i>
                            </a>

                            <a href="javascript:;" class="btn btn-icon btn-lg rounded-pill btn-text-google-plus">
                                <i class="icon-base ri ri-google-fill icon-18px"></i>
                            </a>
                        </div> -->
                    </div>
                </div>
                <!-- /Login -->
                <!-- <img alt="mask" src="{{asset('admin/assets/img/illustrations/auth-basic-login-mask-light.png')}}" class="authentication-image d-none d-lg-block" /> -->
                 <img alt="mask" src="{{asset('uploads/golitter_logo.png')}}" class="authentication-image d-none d-lg-block" />
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

    <!-- <script src="{{asset('admin/assets/js/main.js')}}"></script> -->

    <!-- Page JS -->
    <script src="{{asset('admin/assets/js/pages-auth.js')}}"></script>


    <script>
        $(document).ready(function(){
            $(document).on('click', '.toggle-password', function() {
                let input = $('#password');
                let icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('ri-eye-off-line').addClass('ri-eye-line'); // 👁️ open eye icon
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('ri-eye-line').addClass('ri-eye-off-line'); // 👁️‍🗨️ closed eye icon
                }
            });
        });
    </script>
  </body>
</html>
