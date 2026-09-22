<?php
/**
 * AutoBazaar end-to-end run.
 *
 *  1. A brand new customer signs up on the website, gets an OTP, verifies it,
 *     shops, saves an address and places an order.
 *  2. Everything that customer did is then looked for in the admin panel, and
 *     every admin screen is opened and its row count reported.
 *  3. The test customer, address and order are deleted again.
 *
 * Nothing that already existed is changed. No SMS reaches a real person: the
 * test number starts with 5, which is not a mobile series in India, so the
 * gateway has nowhere to deliver it.
 *
 * Run it from a command window in the project folder:
 *     php docs/tests/full-flow-test.php
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '1024M');       // the users list alone renders thousands of rows
set_time_limit(0);
register_shutdown_function(function () {
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        echo "\n  FATAL  {$e['message']}\n         {$e['file']}:{$e['line']}\n";
    }
});
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\AutoOtp;
use App\Models\Shop\Address;
use App\Models\Shop\Order;
use App\Models\User;
use App\Models\Setting;
use App\Support\SiteData;
use Illuminate\Support\Facades\DB;

$pass = 0; $fail = 0; $failed = [];

function ok($label, $cond, $extra = '') {
    global $pass, $fail, $failed;
    $cond ? $pass++ : ($fail++ + array_push($failed, $label));
    echo ($cond ? '  PASS  ' : '  FAIL  ') . $label . ($extra !== '' ? "   [$extra]" : '') . "\n";
}
function section($t) { echo "\n== $t\n"; }

function req($kernel, $app, $method, $uri, $data = [], $user = null, $json = false) {
    $app['auth']->forgetGuards();
    SiteData::flush();
    Setting::flushCache();
    $server = $json ? ['HTTP_ACCEPT' => 'application/json', 'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'] : [];
    $r = Illuminate\Http\Request::create($uri, $method, $data, [], [], $server);
    if ($user) { $app['auth']->guard('web')->setUser($user); }
    $app->instance('request', $r);
    $res = $kernel->handle($r);

    return [$res->getStatusCode(), $res->getContent(), $res->headers->get('Location')];
}

$app['session']->start();

// Signing in regenerates the session, and with it the form token, so it is read
// fresh before every POST instead of being captured once.
$tok = fn () => $app['session']->token();

$super = User::whereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))->first();
$phone = '5000000001';

/* Leave nothing behind from an interrupted earlier run. */
$old = User::where('phone_number', $phone)->first();
if ($old) {
    Order::where('user_id', $old->id)->delete();
    Address::where('user_id', $old->id)->delete();
    $old->forceDelete();
}
AutoOtp::where('phone', $phone)->delete();

$createdOrderId = null;

try {

/* ============================================================ 1. SIGN UP */
section('A NEW CUSTOMER SIGNS UP');

[$s, $h] = req($kernel, $app, 'GET', '/user/register');
ok('the sign-up page opens', $s === 200, "status=$s");
ok('it is the new design, inside the website', str_contains($h, 'ab-auth-brand') && str_contains($h, '</footer>'));
ok('it lists the districts', substr_count($h, '<option value="') > 3, substr_count($h, '<option value="') . ' options');

$cityId = (int) DB::table('tbl_auto_cities')->orderBy('id')->value('id');
[$s, $b] = req($kernel, $app, 'GET', "/user/areas/{$cityId}", [], null, true);
$areas = json_decode($b, true);
ok('picking a district loads its areas', $s === 200 && ! empty($areas['areas']), count($areas['areas'] ?? []) . ' areas');
$areaId = (int) ($areas['areas'][0]['id'] ?? 1);

[$s, $b] = req($kernel, $app, 'POST', '/user/otp/send', [
    '_token' => $tok(), 'type' => 'register', 'name' => 'Flow Test Customer',
    'phone_number' => $phone, 'auto_area_id' => $areaId,
], null, true);
$sent = json_decode($b, true);
ok('sign-up is accepted and an OTP is issued', ($sent['success'] ?? false) === true, $sent['message'] ?? substr($b, 0, 120));

$customer = User::where('phone_number', $phone)->first();
ok('the customer record is created', (bool) $customer, $customer ? "id={$customer->id}, name={$customer->name}" : 'missing');

// If the SMS gateway refused the unroutable test number, carry on from the
// same place the real flow would: a pending code waiting to be typed in.
if (! AutoOtp::where('phone', $phone)->where('status', 'pending')->exists()) {
    AutoOtp::create(['phone' => $phone, 'otp' => 1234, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()]);
    echo "  note  the gateway would not take a test number, so the code was seeded locally\n";
}
$code = (string) AutoOtp::where('phone', $phone)->value('otp');

[$s, $b] = req($kernel, $app, 'POST', '/user/otp/send', [
    '_token' => $tok(), 'type' => 'register', 'name' => 'Someone Else',
    'phone_number' => $phone, 'auto_area_id' => $areaId,
], null, true);
$again = json_decode($b, true);
ok('signing up twice with the same number is refused', ($again['success'] ?? true) === false, $again['message'] ?? '');

/* ======================================================== 2. VERIFY CODE */
section('THE CUSTOMER TYPES IN THE CODE');

[$s, $h] = req($kernel, $app, 'GET', "/user/otp/{$phone}/register");
ok('the code page opens', $s === 200, "status=$s");
ok('the number is carried into the form (the old page lost it here)',
   str_contains($h, 'name="phone_number" value="' . $phone . '"'));
ok('the page offers a resend and a way back', str_contains($h, 'ab-otp-resend') && str_contains($h, 'change number'));

[$s, $b] = req($kernel, $app, 'POST', '/user/otp/verify',
    ['_token' => $tok(), 'phone_number' => $phone, 'type' => 'register', 'otp' => '0000'], null, true);
$wrong = json_decode($b, true);
ok('a wrong code is rejected', ($wrong['success'] ?? true) === false, $wrong['message'] ?? '');

[$s, $b] = req($kernel, $app, 'POST', '/user/otp/verify',
    ['_token' => $tok(), 'phone_number' => $phone, 'type' => 'register', 'otp' => $code], null, true);
$done = json_decode($b, true);
ok('the right code completes the sign-up', ($done['success'] ?? false) === true, $done['message'] ?? '');
ok('and sends the customer into the site', ! empty($done['redirect_url']), $done['redirect_url'] ?? '-');

$customer = User::where('phone_number', $phone)->first();

/* ============================================================ 3. SIGN IN */
section('THE SAME CUSTOMER SIGNS IN AGAIN LATER');

[$s, $b] = req($kernel, $app, 'POST', '/user/otp/send',
    ['_token' => $tok(), 'type' => 'login', 'phone_number' => $phone], null, true);
$login = json_decode($b, true);
ok('sign-in recognises the number', ($login['success'] ?? false) === true, $login['message'] ?? '');

if (! AutoOtp::where('phone', $phone)->where('status', 'pending')->exists()) {
    AutoOtp::where('phone', $phone)->update(['otp' => 4321, 'status' => 'pending', 'updated_at' => now()]);
}
$code = (string) AutoOtp::where('phone', $phone)->value('otp');

[$s, $b] = req($kernel, $app, 'POST', '/user/otp/verify',
    ['_token' => $tok(), 'phone_number' => $phone, 'type' => 'login', 'otp' => $code], null, true);
ok('the code signs them in', (json_decode($b, true)['success'] ?? false) === true);

[$s, $b] = req($kernel, $app, 'POST', '/user/otp/send',
    ['_token' => $tok(), 'type' => 'login', 'phone_number' => '5000000009'], null, true);
ok('an unknown number is told to register first', (json_decode($b, true)['success'] ?? true) === false);

/* =========================================================== 4. THE SITE */
section('WHAT THE SIGNED-IN CUSTOMER CAN DO');

[$s, $h] = req($kernel, $app, 'GET', '/account', [], $customer);
ok('My Account opens', $s === 200, "status=$s");
ok('it greets them by name', str_contains($h, 'Flow Test'));

[$s, $h] = req($kernel, $app, 'GET', '/accessories/shop', [], $customer);
ok('the accessories shop lists products', substr_count($h, 'Add to Cart') > 3, substr_count($h, 'Add to Cart') . ' buy buttons');
preg_match('/productModelId:\s*(\d+)/', $h, $m);
$productId = (int) ($m[1] ?? 0);
ok('a real product can be picked off the shelf', $productId > 0, "product_model_id=$productId");

[$s, $b] = req($kernel, $app, 'POST', '/cart/add',
    ['_token' => $tok(), 'product_model_id' => $productId, 'qty' => 2], $customer, true);
ok('adding it to the cart works', $s === 200, "status=$s");

[$s, $h] = req($kernel, $app, 'GET', '/cart', [], $customer);
ok('the cart page shows the item', $s === 200 && ! str_contains($h, 'Your cart is empty'), "status=$s");

[$s, $b] = req($kernel, $app, 'POST', '/checkout/address', [
    '_token' => $tok(), 'label' => 'Home', 'name' => 'Flow Test Customer', 'mobile' => '9000000002',
    'address_line_1' => '7 Test Street', 'city' => 'Thiruvallur', 'district' => 'Thiruvallur',
    'state' => 'Tamil Nadu', 'pincode' => '600066',
], $customer);
$address = Address::where('user_id', $customer->id)->latest('id')->first();
ok('a delivery address can be saved', (bool) $address, $address ? "address id={$address->id}" : 'not saved');

[$s, $h] = req($kernel, $app, 'GET', '/checkout', [], $customer);
ok('checkout opens with that address', $s === 200 && str_contains($h, '7 Test Street'), "status=$s");

[$s, $b, $loc] = req($kernel, $app, 'POST', '/checkout/place-order', [
    '_token' => $tok(), 'address_id' => $address?->id, 'delivery_option' => 'standard', 'payment_mode' => 'cod',
], $customer);
$order = Order::where('user_id', $customer->id)->latest('id')->first();
$createdOrderId = $order?->id;
ok('the order is placed', (bool) $order, $order ? "order {$order->order_number}, Rs {$order->total_amount}, {$order->order_status}" : "status=$s");

[$s, $h] = req($kernel, $app, 'GET', '/account/orders', [], $customer);
ok('it shows up in My Orders', $s === 200 && $order && str_contains($h, $order->order_number), "status=$s");

[$s, $h] = req($kernel, $app, 'GET', '/cart', [], $customer);
ok('the cart is empty again after ordering', str_contains($h, 'Your cart is empty') || ! str_contains($h, 'Place Order'));

/* ======================================================== 5. IN THE ADMIN */
section('THE SAME CUSTOMER AND ORDER, SEEN FROM THE ADMIN PANEL');

[$s, $h] = req($kernel, $app, 'GET', '/admin-search?q=' . $phone, [], $super, true);
ok('the admin search finds the new customer by number', $s === 200 && str_contains($h, 'Flow Test'), "status=$s");

[$s, $h] = req($kernel, $app, 'GET', '/ecommerce/orders', [], $super);
ok('the order is listed under E-commerce → Orders', $order && str_contains($h, $order->order_number), "status=$s");

if ($order) {
    [$s, $h] = req($kernel, $app, 'GET', '/ecommerce/orders/view/' . $order->id, [], $super);
    ok('opening the order shows the customer and the address', $s === 200 && str_contains($h, '7 Test Street'), "status=$s");

    [$s] = req($kernel, $app, 'POST', '/ecommerce/orders/status-update',
        ['_token' => $tok(), 'id' => $order->id, 'order_status' => 'confirmed'], $super);
    $moved = Order::find($order->id);
    ok('the order status can be moved on from the admin', $moved?->order_status === 'confirmed', "status=$s, now={$moved?->order_status}");

    [$s] = req($kernel, $app, 'POST', '/ecommerce/orders/status-update',
        ['_token' => $tok(), 'id' => $order->id, 'order_status' => 'placed'], $super);
    $back = Order::find($order->id);
    ok('but it cannot be moved backwards', $back?->order_status === 'confirmed', "still {$back?->order_status}");

    [$s] = req($kernel, $app, 'POST', '/ecommerce/orders/payment-update',
        ['_token' => $tok(), 'id' => $order->id, 'payment_status' => 'paid'], $super);
    $paid = Order::find($order->id);
    ok('payment can be marked received', $paid?->payment_status === 'paid', "now={$paid?->payment_status}");
}

// The admin links to a profile with the id encrypted, exactly as the list does.
$ref = \Illuminate\Support\Facades\Crypt::encryptString((string) $customer->id);
[$s, $h] = req($kernel, $app, 'GET', '/user-management/users-info/' . urlencode($ref), [], $super);
ok('the customer profile page opens in the admin', $s === 200, "status=$s");

/* ===== 6. every admin screen: run in its own process, at the end of this file */

} finally {
    /* ============================================================ CLEAN UP */
    section('PUTTING EVERYTHING BACK');

    $me = User::where('phone_number', $phone)->first();
    if ($me) {
        if ($createdOrderId) {
            DB::table('shop_order_items')->where('order_id', $createdOrderId)->delete();
            DB::table('shop_order_status_histories')->where('order_id', $createdOrderId)->delete();
        }
        DB::table('shop_orders')->where('user_id', $me->id)->delete();
        DB::table('shop_addresses')->where('user_id', $me->id)->delete();
        DB::table('shop_cart_items')->whereIn('cart_id', DB::table('shop_carts')->where('user_id', $me->id)->pluck('id'))->delete();
        DB::table('shop_carts')->where('user_id', $me->id)->delete();
        $me->forceDelete();
    }
    AutoOtp::where('phone', $phone)->delete();

    ok('the test customer, address and order are removed',
       ! User::where('phone_number', $phone)->exists()
       && ! ($createdOrderId && DB::table('shop_orders')->where('id', $createdOrderId)->exists()));
}

/* ============================================= EVERY ADMIN SCREEN OPENS */
/*
 * Run as a separate process on purpose. A browser gives every page request a
 * process of its own; pushing fifty full page renders through this one after
 * the whole shopping journey is not something the live site ever does.
 */
section('EVERY ADMIN SCREEN OPENS AND SHOWS ITS DATA');
$adminExit = 1;
passthru(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__DIR__ . '/admin-screens.php'), $adminExit);
ok('every admin screen opens cleanly', $adminExit === 0, $adminExit === 0 ? 'see the table above' : 'see the problems above');

echo "\n==================== RESULT: $pass passed, $fail failed\n";
if ($failed) { echo "failed: " . implode(' | ', $failed) . "\n"; }
exit($fail ? 1 : 0);
