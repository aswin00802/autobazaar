@extends('site.layout')

@section('title', 'Verify Your Number')
@section('description', 'Enter the 4-digit code we sent to your mobile number to finish signing in to your AutoBazaar account.')
@section('robots', 'noindex, nofollow')

@section('content')

@php
    // 9876543210 -> 98765 43210, easier to check at a glance against the phone.
    $prettyPhone = preg_match('/^\d{10}$/', $phone_number)
        ? substr($phone_number, 0, 5) . ' ' . substr($phone_number, 5)
        : $phone_number;
    $backTo = $type === 'register' ? route('user.register') : route('user.login');
@endphp

<x-ui.auth-shell
    heading="Just one more step"
    lede="We sent a 4-digit code to your mobile. Enter it below and you are in."
    :points="[
        ['clock', 'The code works for 10 minutes'],
        ['refresh', 'Not arrived? Ask for a new one below'],
        ['lock', 'AutoBazaar staff will never ask you for this code'],
    ]"
    footnote="Trouble signing in? Call us and we will sort it out.">

    <h1 class="text-2xl font-extrabold tracking-tight">Enter the code</h1>
    <p class="mt-1.5 text-sm text-muted">
        Sent to <span class="font-semibold text-ink">+91 {{ $prettyPhone }}</span>
        &middot; <a href="{{ $backTo }}" class="font-semibold text-brand-500 hover:underline">change number</a>
    </p>

    <form id="ab-otp-form" method="POST" novalidate class="mt-7">
        @csrf
        <input type="hidden" name="phone_number" value="{{ $phone_number }}">
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="otp" id="fullOtp">

        <div class="ab-otp-row" role="group" aria-label="4-digit code">
            @foreach (range(1, 4) as $i)
                <input type="text" inputmode="numeric" maxlength="1" class="ab-otp-box"
                       aria-label="Digit {{ $i }}" autocomplete="{{ $i === 1 ? 'one-time-code' : 'off' }}"
                       @if ($i === 1) autofocus @endif>
            @endforeach
        </div>

        <p id="ab-auth-msg" class="ab-auth-msg mt-4" role="alert" aria-live="polite" hidden></p>

        <button type="submit" class="ab-btn ab-btn-primary mt-4 w-full py-3">Verify &amp; Continue</button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        Didn't get the code?
        <button type="button" id="ab-otp-resend" class="font-semibold text-brand-500 hover:underline disabled:text-muted disabled:no-underline" disabled>
            Resend
        </button>
        <span id="ab-otp-timer" class="text-muted"></span>
    </p>

    <style>
        .ab-otp-row { display: flex; gap: 12px; }
        .ab-otp-box {
            width: 56px; height: 60px; text-align: center; font-size: 22px; font-weight: 700;
            border: 1px solid var(--color-line, #E3E8E5); border-radius: 12px; background: #fff; color: #10231A;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .ab-otp-box:focus { outline: none; border-color: #0E7A4D; box-shadow: 0 0 0 3px rgba(14, 122, 77, .14); }
        .ab-otp-box.is-filled { border-color: #0E7A4D; }
    </style>

    <script>
    (function () {
        var form   = document.getElementById('ab-otp-form');
        var boxes  = Array.prototype.slice.call(form.querySelectorAll('.ab-otp-box'));
        var hidden = document.getElementById('fullOtp');
        var button = form.querySelector('button[type="submit"]');
        var msg    = document.getElementById('ab-auth-msg');
        var resend = document.getElementById('ab-otp-resend');
        var timer  = document.getElementById('ab-otp-timer');

        function say(text, kind) { msg.textContent = text; msg.className = 'ab-auth-msg mt-4 is-' + kind; msg.hidden = false; }
        function code() { return boxes.map(function (b) { return b.value; }).join(''); }

        boxes.forEach(function (box, i) {
            box.addEventListener('input', function () {
                this.value = this.value.replace(/\D+/g, '').slice(0, 1);
                this.classList.toggle('is-filled', this.value !== '');
                msg.hidden = true;
                if (this.value && i < boxes.length - 1) { boxes[i + 1].focus(); }
                // All four in: no reason to make anyone reach for the button.
                if (code().length === 4) { form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit', { cancelable: true })); }
            });

            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && ! this.value && i > 0) { boxes[i - 1].focus(); }
                if (e.key === 'ArrowLeft' && i > 0) { boxes[i - 1].focus(); }
                if (e.key === 'ArrowRight' && i < boxes.length - 1) { boxes[i + 1].focus(); }
            });

            // Most people paste the whole code, or the phone fills it in.
            box.addEventListener('paste', function (e) {
                var digits = (e.clipboardData || window.clipboardData).getData('text').replace(/\D+/g, '').slice(0, 4);
                if (! digits) { return; }
                e.preventDefault();
                boxes.forEach(function (b, n) { b.value = digits[n] || ''; b.classList.toggle('is-filled', !! digits[n]); });
                boxes[Math.min(digits.length, 3)].focus();
                if (digits.length === 4) { form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit', { cancelable: true })); }
            });
        });

        var busy = false;

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            if (busy) { return; }

            if (code().length !== 4) { say('Enter all four digits.', 'error'); return; }

            busy = true;
            hidden.value = code();
            button.classList.add('ab-auth-busy');
            button.textContent = 'Verifying…';

            fetch(@json(route('user.otp.verify')), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: new FormData(form),
            })
            .then(function (r) {
                if (r.status === 429) { throw new Error('Too many attempts. Please wait a minute and try again.'); }
                return r.json().catch(function () { throw new Error('Something went wrong. Please try again.'); });
            })
            .then(function (data) {
                if (data.success === true || (data.success === 'invalid' && data.redirect_url)) {
                    say(data.message || 'Verified. Taking you in…', data.success === true ? 'ok' : 'error');
                    window.location.href = data.redirect_url;
                    return;
                }
                throw new Error(data.message || 'That code did not match.');
            })
            .catch(function (err) {
                say(err.message, 'error');
                busy = false;
                button.classList.remove('ab-auth-busy');
                button.textContent = 'Verify & Continue';
                boxes.forEach(function (b) { b.value = ''; b.classList.remove('is-filled'); });
                boxes[0].focus();
            });
        });

        /* ---- Resend -------------------------------------------------------
           Always sent as a login request: by this point the account exists
           (a sign-up creates it before the first code goes out), and a second
           'register' request would be refused as "already registered".      */
        var left = 30;
        function tick() {
            timer.textContent = left > 0 ? 'in ' + left + 's' : '';
            resend.disabled = left > 0;
            if (left-- > 0) { setTimeout(tick, 1000); }
        }
        tick();

        resend.addEventListener('click', function () {
            resend.disabled = true;
            timer.textContent = 'sending…';

            var body = new FormData();
            body.append('_token', form.querySelector('input[name="_token"]').value);
            body.append('phone_number', @json($phone_number));
            body.append('type', 'login');

            fetch(@json(route('user.otp.send')), {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: body,
            })
            .then(function (r) {
                if (r.status === 429) { throw new Error('Too many attempts. Please wait a minute and try again.'); }
                return r.json();
            })
            .then(function (data) {
                if (! data.success) { throw new Error(data.message || 'Could not send a new code.'); }
                say('A new code is on its way.', 'ok');
                boxes.forEach(function (b) { b.value = ''; b.classList.remove('is-filled'); });
                boxes[0].focus();
                left = 30; tick();
            })
            .catch(function (err) { say(err.message, 'error'); timer.textContent = ''; resend.disabled = false; });
        });
    })();
    </script>
</x-ui.auth-shell>

@endsection
