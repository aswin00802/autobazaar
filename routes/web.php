<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web routes
|--------------------------------------------------------------------------
|
|   routes/site.php   customer website (home, catalogue, shop, account)
|   routes/web.php    customer auth, policy pages, admin auth   <- this file
|   routes/admin.php  admin panel
|
*/

// Customer website (new AutoBazaar frontend).
require __DIR__ . '/site.php';

/* ---------------------------------------------------------- Customer auth (OTP) */
Route::get('user/login', [App\Http\Controllers\Web\Auth\LoginController::class, 'index'])->name('user.login');
Route::get('user/register', [App\Http\Controllers\Web\Auth\RegisterController::class, 'index'])->name('user.register');
Route::get('user/areas/{city}', [App\Http\Controllers\Web\Auth\RegisterController::class, 'areas'])->whereNumber('city')->name('user.areas');
Route::get('/user/otp/{mobile}/{otp_type}', [App\Http\Controllers\Web\Auth\OtpController::class, 'Otp_page'])->name('user.otp-page');
Route::post('/user/otp/send', [App\Http\Controllers\Web\Auth\OtpController::class, 'sendOTP'])->middleware('throttle:5,1')->name('user.otp.send');
Route::post('/user/otp/verify', [App\Http\Controllers\Web\Auth\OtpController::class, 'verifyOTP'])->middleware('throttle:10,1')->name('user.otp.verify');

/* ------------------------------------------- Policy and account-deletion pages
   These URLs are linked from the Play Store / App Store listings, so the paths
   must not change. */
Route::get('/terms-conditions', [App\Http\Controllers\Web\SiteController::class, 'page'])->defaults('slug', 'terms-conditions')->name('terms-conditions');
Route::get('/privacy-policy', [App\Http\Controllers\Web\SiteController::class, 'page'])->defaults('slug', 'privacy-policy')->name('privacy-policy');
Route::get('account-delete', [App\Http\Controllers\Web\WebpageController::class, 'accountDelete'])->name('account-delete');
Route::post('account-delete-store', [App\Http\Controllers\Web\WebpageController::class, 'accountDeleteStore'])->name('account-delete-store');

// FairPrice app listing
Route::get('fareprice/account-delete', [App\Http\Controllers\Fareprice\AccountController::class, 'farepriceAccountDelete'])->name('fareprice.account-delete');
Route::post('fareprice/account-delete-store', [App\Http\Controllers\Fareprice\AccountController::class, 'farepriceaccountDeleteStore'])->name('fareprice.account-delete-store');
Route::get('fareprice/privacy-policy', [App\Http\Controllers\Fareprice\AccountController::class, 'farepricePrivacyPolicy'])->name('fareprice.privacy-policy');
Route::get('fareprice/terms-condition', [App\Http\Controllers\Fareprice\AccountController::class, 'farepriceTermsCondition'])->name('fareprice.terms-condition');

/* ------------------------------------------------------------------ Admin auth */
Route::post('/auth/send-otp', [App\Http\Controllers\Auth\OTPController::class, 'otpSend'])->name('auth.send-otp');
Route::post('/auth/verify-otp', [App\Http\Controllers\Auth\OTPController::class, 'verifyOtp'])->name('auth.verify-otp');
Route::get('login/{provider}/redirect', [App\Http\Controllers\Auth\WebSocialLoginController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [App\Http\Controllers\Auth\WebSocialLoginController::class, 'handleProviderCallback']);
Route::post('admin/logout', [App\Http\Controllers\Auth\LoginController::class, 'admin_logout'])->name('admin.logout');

// login, logout (POST), register, password reset — shared by admin and customers.
Auth::routes(['register' => false]);

// Admin panel (auth + active-user check).
require __DIR__ . '/admin.php';
