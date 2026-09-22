@extends('site.layout')

@section('title', 'Create Account')
@section('description', 'Create your free AutoBazaar account with your mobile number to save autos, send enquiries, order accessories and follow your orders.')
@section('robots', 'noindex, follow')

@section('content')

<x-ui.auth-shell
    heading="Create your free account"
    lede="It takes under a minute. Tell us where you are so we can show you the right showroom, stock and offers."
    :points="[
        ['rupee', 'Price and EMI enquiries answered by our team'],
        ['truck', 'Order genuine accessories and track delivery'],
        ['scales', 'Save autos and compare them side by side'],
        ['headset', 'Support in your district, not a call centre'],
    ]">

    <h1 class="text-2xl font-extrabold tracking-tight">Create account</h1>
    <p class="mt-1.5 text-sm text-muted">We will text a 4-digit code to confirm your number.</p>

    <form id="ab-auth-form" method="POST" novalidate class="mt-7 space-y-4">
        @csrf
        <input type="hidden" name="type" value="register">

        <div>
            <label for="name" class="ab-label">Your Name</label>
            <input id="name" name="name" type="text" autocomplete="name" required
                   placeholder="Name we should greet you by" class="ab-field" value="{{ old('name') }}">
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="auto_city_id" class="ab-label">District</label>
                <select id="auto_city_id" class="ab-field" aria-describedby="ab-area-hint">
                    <option value="">Select your district</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                    <option value="0">Other district</option>
                </select>
            </div>

            <div>
                <label for="auto_area_id" class="ab-label">Area</label>
                <select id="auto_area_id" name="auto_area_id" class="ab-field" required disabled>
                    <option value="">Choose a district first</option>
                </select>
            </div>
        </div>
        <p id="ab-area-hint" class="-mt-1 text-[11px] text-muted">Areas load once you pick a district.</p>

        <div>
            <label for="phone_number" class="ab-label">Mobile Number</label>
            <div class="ab-phone">
                <span class="cc">+91</span>
                <input id="phone_number" name="phone_number" type="tel" inputmode="numeric"
                       autocomplete="tel-national" maxlength="10" required
                       placeholder="10-digit mobile number" class="ab-field" value="{{ old('phone_number') }}">
            </div>
        </div>

        <p id="ab-auth-msg" class="ab-auth-msg" role="alert" aria-live="polite" hidden></p>

        <button type="submit" class="ab-btn ab-btn-primary w-full py-3">Send OTP</button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        Already registered?
        <a href="{{ route('user.login') }}" class="font-semibold text-brand-500 hover:underline">Sign in</a>
    </p>

    <p class="mt-6 border-t border-line pt-5 text-xs text-muted">
        By creating an account you agree to our
        <a href="{{ route('terms-conditions') }}" class="font-semibold text-brand-500 hover:underline">Terms</a> and
        <a href="{{ route('privacy-policy') }}" class="font-semibold text-brand-500 hover:underline">Privacy Policy</a>.
    </p>

    <script>
    // Areas are fetched for the chosen district only — the full list is 16,000+ rows.
    (function () {
        var district = document.getElementById('auto_city_id');
        var area = document.getElementById('auto_area_id');

        district.addEventListener('change', function () {
            area.innerHTML = '<option value="">Loading areas…</option>';
            area.disabled = true;

            if (this.value === '') {
                area.innerHTML = '<option value="">Choose a district first</option>';
                return;
            }

            fetch(@json(url('user/areas')) + '/' + encodeURIComponent(this.value), { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    area.innerHTML = '<option value="">Select your area</option>';
                    (data.areas || []).forEach(function (a) {
                        var o = document.createElement('option');
                        o.value = a.id;
                        o.textContent = a.name;
                        area.appendChild(o);
                    });
                    area.disabled = false;
                })
                .catch(function () {
                    area.innerHTML = '<option value="">Could not load areas — reload the page</option>';
                });
        });
    })();
    </script>

    @include('web.auth.partials.send-otp', ['requireArea' => true])
</x-ui.auth-shell>

@endsection
