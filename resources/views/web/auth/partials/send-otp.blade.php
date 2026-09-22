{{--
    Posts the sign-in / sign-up form to user.otp.send and, when the code is on
    its way, moves to the OTP screen.

    Same endpoint and same field names as before — only the page around it is new.
    Messages are shown in the form itself instead of a pop-up, so nothing is
    loaded from a CDN and the text stays readable while the number is corrected.
--}}
<script>
(function () {
    var form = document.getElementById('ab-auth-form');
    if (! form) { return; }

    var phone  = form.querySelector('#phone_number');
    var button = form.querySelector('button[type="submit"]');
    var msg    = document.getElementById('ab-auth-msg');
    var label  = button ? button.textContent : '';

    function say(text, kind) {
        msg.textContent = text;
        msg.className = 'ab-auth-msg is-' + kind;
        msg.hidden = false;
    }
    function clear() { msg.hidden = true; msg.textContent = ''; }

    // Typing anything but digits into a mobile box is always a mistake.
    if (phone) {
        phone.addEventListener('input', function () {
            var digits = this.value.replace(/\D+/g, '').slice(0, 10);
            if (this.value !== digits) { this.value = digits; }
            clear();
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var number = phone ? phone.value.replace(/\D+/g, '') : '';
        if (number.length !== 10) {
            say('Enter your 10-digit mobile number.', 'error');
            if (phone) { phone.focus(); }
            return;
        }
        @if (isset($requireArea) && $requireArea)
            if (! form.querySelector('#name').value.trim()) {
                say('Enter the name we should greet you by.', 'error');
                form.querySelector('#name').focus();
                return;
            }
            if (! form.querySelector('#auto_area_id').value) {
                say('Choose your district, then your area.', 'error');
                form.querySelector('#auto_city_id').focus();
                return;
            }
        @endif

        button.classList.add('ab-auth-busy');
        button.textContent = 'Sending OTP…';
        clear();

        fetch(@json(route('user.otp.send')), {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            body: new FormData(form),
        })
        .then(function (r) {
            if (r.status === 429) { throw new Error('Too many attempts. Please wait a minute and try again.'); }
            return r.json().catch(function () { throw new Error('Something went wrong. Please try again.'); });
        })
        .then(function (data) {
            if (data.success === true) {
                say('OTP sent. Taking you to the next step…', 'ok');
                window.location.replace(
                    @json(url('user/otp')) + '/' + encodeURIComponent(data.mobile) + '/' + encodeURIComponent(data.otp_type)
                );
                return;
            }
            throw new Error(data.message || 'Could not send the OTP. Please check the number.');
        })
        .catch(function (err) {
            say(err.message, 'error');
            button.classList.remove('ab-auth-busy');
            button.textContent = label;
        });
    });
})();
</script>
