<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification - JP Autozone</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if(getSetting('fav_icon'))
        <link rel="icon" type="image/x-icon" href="{{ asset(getSetting('fav_icon')) }}" />
    @else
        <link rel="icon" type="image/x-icon" href="{{asset('assets/images/favicon-32x32.png')}}" />
    @endif
    <!-- <link rel="icon" href="{{ url('/') }}/assets/images/favicon-32x32.png" type="image/png" /> -->

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('css/auth.css')}}">

</head>
<body>

    <!-- Tagline -->
    <div class="tagline">Verify Your OTP</div>

    <!-- OTP Box -->
    <div class="otp-box">
        <h5 class="text-center mb-3">Enter OTP sent to your mobile {{ $phone_number }}</h5>
        <form name="opt_form" id="opt_form" class="opt_form" method="POST">
            @csrf
            <div class="d-flex justify-content-center mb-3">
                <input type="hidden" id="phone_number" name="phone_number" value="{{$phone_number}}">
                <input type="hidden" id="type" name="type" value="{{$type}}">
                <input type="text" maxlength="1" class="otp-input" name="opt_1" id="opt_1" required>
                <input type="text" maxlength="1" class="otp-input" name="opt_2" id="opt_2" required>
                <input type="text" maxlength="1" class="otp-input" name="opt_3" id="opt_3" required>
                <input type="text" maxlength="1" class="otp-input" name="opt_4" id="opt_4" required>
                <input type="hidden" name="otp" id="fullOtp" />
            </div>

            <button type="submit" class="btn btn-primary w-100 rounded-pill">Verify OTP</button>

        </form>
    </div>

    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Auto-focus next input -->
    <script>
        document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && index > 0 && !input.value) {
                    inputs[index - 1].focus();
                }
            });
        });
    </script>
    <!-- timer -->
    <!-- <script>
        let seconds = 30;

        const countdownElement = document.getElementById('countdown');

        const timer = setInterval(() => {
            seconds--;

            // Format seconds as MM:SS
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;

            countdownElement.textContent =
                `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;

            if (seconds <= 0) {
                clearInterval(timer);
                countdownElement.textContent = "00:00";
                // Optionally enable the "Resend OTP" button here
            }
        }, 1000);
    </script> -->

    <script type="text/javascript">
        $('.opt_form').on('submit', function(e) {
            e.preventDefault();
            $('#phone_number').val('')
            $('#type').val('')
            $('#fullOtp').val('')
            let otp1 = $('#opt_1').val()
            let otp2 = $('#opt_2').val()
            let otp3 = $('#opt_3').val()
            let otp4 = $('#opt_4').val()
            let otp = otp1 + otp2 + otp3 + otp4

            //to get url parameter
            const pathname = window.location.pathname;
            // Example: "/otp/123/login"
            const segments = pathname.split('/');
            const mobile = segments[6]; // assuming "/otp/{mobile}/{type}"
            const type = segments[7];
            //to set hidden input value
            $('#fullOtp').val(otp)
            $('#phone_number').val(mobile)
            $('#type').val(type)

            $.ajax({
                url: "{{ route('user.otp.verify') }}",
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
                            window.location.href = response.redirect_url;
                        });
                    } else if (response.success == false) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            timer: 2000,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false
                        });
                    } else if (response.success == 'invalid') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                            timer: 2000,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = response.redirect_url;
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
    </script>

</body>
</html>
