@extends('site.layout')

@section('title', 'Sign In')
@section('description', 'Sign in to AutoBazaar with your mobile number and a one-time code to track your enquiries, orders, saved autos and addresses.')
@section('robots', 'noindex, follow')

@section('content')

<x-ui.auth-shell
    heading="Welcome back to AutoBazaar"
    lede="One app for all auto needs — sales, spares and support. Sign in with your mobile number; there is no password to remember."
    :points="[
        ['doc', 'Follow your enquiries and quotations'],
        ['cart', 'See your accessory orders and delivery status'],
        ['heart', 'Keep your saved autos and comparisons'],
        ['shield', 'A fresh code every time — nothing to leak'],
    ]">

    <h1 class="text-2xl font-extrabold tracking-tight">Sign in</h1>
    <p class="mt-1.5 text-sm text-muted">We will text a 4-digit code to your mobile.</p>

    <form id="ab-auth-form" method="POST" novalidate class="mt-7 space-y-4">
        @csrf
        <input type="hidden" name="type" value="login">

        <div>
            <label for="phone_number" class="ab-label">Mobile Number</label>
            <div class="ab-phone">
                <span class="cc">+91</span>
                <input id="phone_number" name="phone_number" type="tel" inputmode="numeric"
                       autocomplete="tel-national" maxlength="10" required autofocus
                       placeholder="10-digit mobile number" class="ab-field"
                       value="{{ old('phone_number') }}">
            </div>
        </div>

        <p id="ab-auth-msg" class="ab-auth-msg" role="alert" aria-live="polite" hidden></p>

        <button type="submit" class="ab-btn ab-btn-primary w-full py-3">Send OTP</button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">
        New here?
        <a href="{{ route('user.register') }}" class="font-semibold text-brand-500 hover:underline">Create an account</a>
    </p>

    <div class="mt-6 flex items-center gap-2 border-t border-line pt-5 text-xs text-muted">
        <x-ui.icon name="lock" :size="14" class="text-brand-500" />
        <span>We never ask for your OTP on a call. Keep it to yourself.</span>
    </div>

    @include('web.auth.partials.send-otp')
</x-ui.auth-shell>

@endsection
