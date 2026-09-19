<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Auto Bazaar</title>
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
        <h3 class="text-center mb-3">Register</h3>
        <form class="singform" method="POST">
            @csrf
            <input type="hidden" name="type" value="register">
            <div class="mb-3">
                <select class="form-control" id="auto_city_id" aria-label="District">
                    <option value="" selected>Select your district</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                    <option value="0">Other district</option>
                </select>
            </div>
            <div class="mb-3">
                <select class="form-control" name='auto_area_id' id="auto_area_id" disabled>
                    <option value="" selected>Select area form dropdown</option>
                </select>
            </div>
            <script>
                // Areas for the chosen district only — the full list is 16,000+ rows.
                document.getElementById('auto_city_id').addEventListener('change', function () {
                    var area = document.getElementById('auto_area_id');
                    area.innerHTML = '<option value="">Loading areas…</option>';
                    area.disabled = true;
                    if (this.value === '') { area.innerHTML = '<option value="">Select area form dropdown</option>'; return; }
                    fetch(@json(url('user/areas')) + '/' + this.value, { headers: { 'Accept': 'application/json' } })
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            area.innerHTML = '<option value="">Select area form dropdown</option>';
                            (data.areas || []).forEach(function (a) {
                                var o = document.createElement('option'); o.value = a.id; o.textContent = a.name; area.appendChild(o);
                            });
                            area.disabled = false;
                        })
                        .catch(function () { area.innerHTML = '<option value="">Could not load areas — reload the page</option>'; });
                });
            </script>
            <div class="mb-3">
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter Username">
            </div>
            <div class="mb-3">
                <input type="text" id="phone_number" name="phone_number" class="form-control" placeholder="Enter Mobile Number">
            </div>

            <button type="submit" class="btn btn-primary login-btn">Send OTP</button>
        </form>
        <p class="pt-3">Already have an account ? <a href="{{ route('user.login') }}">Sign In</a></p>
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
                                // let url = `/user/otp/${encodeURIComponent(response.mobile)}/${encodeURIComponent(response.otp_type)}`;
                                // window.location.replace(url);
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
