<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Auto Bazaar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if(getSetting('fav_icon'))
        <link rel="icon" type="image/x-icon" href="{{ asset(getSetting('fav_icon')) }}" />
    @else
        <link rel="icon" type="image/x-icon" href="{{asset('assets/images/favicon-32x32.png')}}" />
    @endif
    <!-- <link rel="icon" href="{{url('/')}}/assets/images/favicon-32x32.png" type="image/png" /> -->

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('css/auth.css')}}">

</head>
<body>


    <!-- Tagline -->
    <div class="tagline">“One App for All Auto Needs - Sales, Spares & Support.”</div>

    <!-- Login Box -->
    <div class="login-box">
        <h3 class="text-center mb-3">Login</h3>
        <form class="singform" method="POST">
            @csrf
            <input type="hidden" name="type" value="login">
            <input type="hidden" name="auto_area_id" value="1">
            <div class="mb-3">
                <input type="text" id="phone_number" name="phone_number" class="form-control" placeholder="Enter Mobile Number" value="{{ old('phone_number') }}">
                @error('username')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary login-btn">Send OTP</button>
        </form>
        <p class="pt-3">Don’t have an account? <a href="{{ route('user.register') }}">Sign up</a></p>
    </div>

    <!-- Bottom Logo -->
    
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            $('.singform').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('user.otp.send') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success == true) {
                            Swal.fire({
                                icon: 'Success',
                                title: 'OTP!',
                                text: response.message,
                                timer: 2000,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false
                            }).then(function() {
                                let baseUrl = "{{ url('/') }}"; 
                                let url = `${baseUrl}/user/otp/${encodeURIComponent(response.mobile)}/${encodeURIComponent(response.otp_type)}`;
                                window.location.replace(url);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                                timer: 2000,
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false
                            });
                        }

                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let allErrors = Object.values(errors).map(err => err[0]).join('<br>');

                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            html: allErrors
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
