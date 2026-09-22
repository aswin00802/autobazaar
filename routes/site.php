<?php

/**
 * AutoBazaar customer website.
 *
 * Browsing and the cart are public; checkout and the account area require the
 * OTP login at /user/login (UserAuth middleware).
 */

use App\Http\Controllers\Web\SiteController;
use App\Http\Controllers\Web\VehicleLeadController;
use App\Http\Controllers\Web\Shop\CartController;
use App\Http\Controllers\Web\Shop\CheckoutController;
use Illuminate\Support\Facades\Route;

/* Search engines: generated so they always carry the right domain and catalogue */
Route::get('/sitemap.xml', [\App\Http\Controllers\Web\SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [\App\Http\Controllers\Web\SeoController::class, 'robots'])->name('robots');

Route::name('site.')->group(function () {
    Route::get('/', [SiteController::class, 'home'])->name('home');

    /* ------------------------------------------------------------ vehicles */
    Route::get('/new-autos', [SiteController::class, 'newAutos'])->name('new-autos');
    Route::get('/used-autos', [SiteController::class, 'usedAutos'])->name('used-autos');
    Route::get('/used-autos/{id}/{slug?}', [SiteController::class, 'usedAuto'])->whereNumber('id')->name('used-auto');
    Route::get('/new-autos/{brand}', [SiteController::class, 'brand'])->name('brand');
    Route::get('/new-autos/{brand}/{model}', [SiteController::class, 'model'])->name('model');

    /* ------------------------------------ vehicle leads + reviews (public) */
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/vehicles/{slug}/lead', [VehicleLeadController::class, 'storeLead'])->name('vehicles.lead');
        Route::post('/vehicles/{slug}/review', [VehicleLeadController::class, 'storeReview'])->name('vehicles.review');
    });

    /* ------------------------------------------------------------- compare */
    Route::get('/compare', [SiteController::class, 'compare'])->name('compare');
    Route::get('/compare/{combo}', [SiteController::class, 'compare'])->name('compare.combo');

    /* ------------------------------------------------------ buying journey */
    Route::get('/buying-options', [SiteController::class, 'buyingOptions'])->name('buying-options');
    Route::get('/enquiry/{model?}', [SiteController::class, 'enquiry'])->name('enquiry');

    /* -------------------------------------------------------------- content */
    Route::get('/offers', [SiteController::class, 'offers'])->name('offers');
    Route::get('/finance-emi', [SiteController::class, 'finance'])->name('finance');
    Route::get('/government-schemes', [SiteController::class, 'schemes'])->name('schemes');
    Route::get('/auto-news', [SiteController::class, 'news'])->name('news');
    Route::get('/auto-news/{slug}', [SiteController::class, 'newsArticle'])->name('news.article');
    Route::get('/app', [SiteController::class, 'app'])->name('app');

    /* ---------------------------------------------------------- accessories */
    Route::get('/accessories', [SiteController::class, 'accessories'])->name('accessories');
    Route::get('/accessories/shop', [SiteController::class, 'shop'])->name('accessories.shop');

    /* ------------------------------------------------- cart (public, guests ok) */
    // Guests and customers may change the cart; staff accounts may only look (see CustomerOnly).
    Route::middleware('customer.only')->group(function () {
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
        Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])->name('cart.coupon');
        Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');
    });
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::get('/cart', [CartController::class, 'show'])->name('cart');

    /* --------------------------------------- checkout + orders (login required) */
    Route::middleware(['UserAuth', 'customer.only'])->group(function () {
        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
        Route::post('/checkout/address', [CheckoutController::class, 'storeAddress'])->name('checkout.address');
        Route::post('/checkout/place-order', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
        Route::get('/order-confirmed/{orderNumber}', [CheckoutController::class, 'confirmed'])->name('order.confirmed');
    });

    /* ------------------------------------------- account (the customer's own data) */
    Route::middleware(['UserAuth', 'customer.only'])->group(function () {
        Route::get('/account', [SiteController::class, 'account'])->name('account');
        Route::get('/account/orders', [SiteController::class, 'orders'])->name('account.orders');
        Route::get('/account/orders/{id}', [SiteController::class, 'order'])->name('account.order');
        Route::get('/account/{section}', [SiteController::class, 'accountSection'])->name('account.section');
    });

    /* --------------------------------------------------------------- static */
    Route::get('/search', [SiteController::class, 'search'])->name('search');
    Route::get('/about-us', [SiteController::class, 'about'])->name('about');
    Route::get('/contact', [SiteController::class, 'contact'])->name('contact');
    Route::get('/faq', [SiteController::class, 'faq'])->name('faq');
    Route::get('/page/{slug}', [SiteController::class, 'page'])->name('page');
});
