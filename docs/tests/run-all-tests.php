<?php
/**
 * AutoBazaar full test run: SMOKE + UAT + DESIGN.
 * Everything runs through the real app. Any data it changes is put back at the end.
 */
chdir('C:/xampp/htdocs/autobazaar');
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Setting; use App\Models\User; use App\Models\Auto\Auto;
use App\Services\UsedAutoService; use App\Support\SiteData;
use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Cache; use Illuminate\Support\Carbon;

$pass = 0; $fail = 0; $failed = [];
function ok($label, $cond, $extra = '') {
    global $pass, $fail, $failed;
    $cond ? $pass++ : ($fail++ + array_push($failed, $label));
    echo ($cond ? "  PASS  " : "  FAIL  ") . $label . ($extra !== '' ? "   [$extra]" : "") . "\n";
}
function section($t) { echo "\n== $t\n"; }

function req($kernel, $app, $method, $uri, $data = [], $user = null, $json = false) {
    $app['auth']->forgetGuards(); SiteData::flush(); Setting::flushCache();
    $server = $json ? ['HTTP_ACCEPT' => 'application/json', 'HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest'] : [];
    $r = Illuminate\Http\Request::create($uri, $method, $data, [], [], $server);
    if ($user) { $app['auth']->guard('web')->setUser($user); }
    $app->instance('request', $r);
    $res = $kernel->handle($r);
    return [$res->getStatusCode(), $res->getContent(), $res->headers->get('Location')];
}

$snapshot = DB::table('settings')->get()->map(fn ($r) => (array) $r)->all();
$super = User::whereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))->first();
$admin = User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->first();
$customer = User::where('role_id', 1000)->whereDoesntHave('roles')->where('status', 1)->first();
$used = app(UsedAutoService::class);
$listings = $used->all();

try {
/* ================================================================= SMOKE */
section('SMOKE — every public page answers');
$publicPages = ['/', '/new-autos', '/used-autos', '/new-autos/tvs', '/new-autos/tvs/king-deluxe', '/compare',
    '/compare/tvs-king-deluxe-vs-bajaj-re', '/buying-options', '/offers', '/finance-emi', '/government-schemes',
    '/auto-news', '/app', '/accessories', '/accessories/shop', '/about-us', '/contact', '/faq', '/enquiry',
    '/terms-conditions', '/privacy-policy', '/cart', '/search?q=tvs', '/sitemap.xml', '/robots.txt', '/user/login'];
$down = [];
foreach ($publicPages as $p) { [$s] = req($kernel, $app, 'GET', $p); if ($s !== 200) { $down[] = "$p=$s"; } }
ok(count($publicPages) . ' public pages return 200', ! $down, $down ? implode(' ', $down) : 'all ok');
[$s] = req($kernel, $app, 'GET', '/used-autos/' . $listings[0]['id'] . '/' . $listings[0]['slug']);
ok('a used-auto detail page answers', $s === 200, "status=$s");
[$s] = req($kernel, $app, 'GET', '/no-such-page-at-all');
ok('unknown address shows the 404 page', $s === 404, "status=$s");

section('SMOKE — admin answers');
$adminPages = ['/dashboard', '/dashboard/attention', '/admin-search?q=tvs', '/settings/general-settings',
    '/settings/payment-settings', '/settings/smtp-settings', '/user-management/users-list', '/quotation-list',
    '/enquiry-auto-list', '/driver-request-list', '/fairprice/rides', '/fairprice/fare-settings', '/ecommerce/orders'];
$down = [];
foreach ($adminPages as $p) { [$s] = req($kernel, $app, 'GET', $p, [], $super); if ($s !== 200) { $down[] = "$p=$s"; } }
ok(count($adminPages) . ' admin pages return 200', ! $down, $down ? implode(' ', $down) : 'all ok');

section('SMOKE — mobile APIs still answer (no app change)');
$apiChecks = [['POST', '/api/auth/user-sendOtp', ['phone' => '9999999999']], ['POST', '/api/auth/user-verifyOtp', ['phone' => '9999999999', 'otp' => '1111', 'fcm_token' => 'x', 'device_id' => 'x']],
    ['GET', '/api/get-auto-brands', []], ['GET', '/api/get-cities', []], ['GET', '/api/get-fuel-types', []]];
$bad = [];
foreach ($apiChecks as [$m, $u, $d]) { [$s, $b] = req($kernel, $app, $m, $u, $d, null, true); $j = json_decode($b, true); if ($s >= 500 || ! is_array($j)) { $bad[] = "$u=$s"; } }
ok('mobile API endpoints answer with JSON, no server errors', ! $bad, $bad ? implode(' ', $bad) : 'all ok');
[$s, $b] = req($kernel, $app, 'GET', '/api/get-profile', [], null, true);
ok('a protected API endpoint still refuses without a token', $s === 401, "status=$s");

/* ================================================================== UAT */
$app['session']->start(); $formToken = $app['session']->token();
section('UAT — a buyer browsing used autos');
[, $h] = req($kernel, $app, 'GET', '/used-autos');
ok('used list shows every live auto from the database', substr_count($h, 'ab-card flex h-full flex-col') === count($listings), count($listings) . ' listings');
ok('prices are the real asking prices in Indian format', str_contains($h, $listings[0]['price_label']));
$withPrice = collect($listings)->where('price', '>', 0)->first();
ok('a buyer can open a listing and see its documents', (function () use ($kernel, $app, $withPrice) {
    [$s, $h] = req($kernel, $app, 'GET', '/used-autos/' . $withPrice['id'] . '/' . $withPrice['slug']);
    return $s === 200 && str_contains($h, 'Vehicle Details') && str_contains($h, 'RC');
})());
ok('WhatsApp button carries the listing id so the team knows the auto', (function () use ($kernel, $app, $withPrice) {
    [, $h] = req($kernel, $app, 'GET', '/used-autos/' . $withPrice['id'] . '/' . $withPrice['slug']);
    return str_contains($h, rawurlencode($withPrice['ref']));
})());

section('UAT — comparing autos');
[, $h] = req($kernel, $app, 'GET', '/compare');
ok('compare starts empty and invites a choice', ! str_contains($h, '<table') && str_contains($h, 'Pick 2 to 4 autos'));
[, $h] = req($kernel, $app, 'GET', '/compare/tvs-king-deluxe-vs-bajaj-re-vs-piaggio-ape-xtra-vs-mahindra-treo-plus');
preg_match_all('/aria-label="Remove ([^"]*) from comparison"/', $h, $m);
ok('four autos compare side by side', count($m[1]) === 4, implode(', ', $m[1]));
preg_match_all('#<a href="([^"]+)"[^>]*aria-label="Remove ([^"]+) from comparison"#s', $h, $rm);
[, $h3] = req($kernel, $app, 'GET', parse_url(html_entity_decode($rm[1][2]), PHP_URL_PATH));
$left = preg_match_all('#<a href="[^"]+"[^>]*aria-label="Remove [^"]+ from comparison"#s', $h3);
ok('closing the 3rd leaves 3 (the old bug: it jumped back to 4)', $left === 3, "$left left");

section('UAT — shopping for accessories');
[, $h] = req($kernel, $app, 'GET', '/accessories/shop');
ok('shop lists products a customer can buy', substr_count($h, 'Add to Cart') > 3);
[$s, $b] = req($kernel, $app, 'POST', '/cart/add', ['_token' => $formToken, 'product_model_id' => 999999, 'quantity' => 1], $customer, true);
ok('adding a product that does not exist is refused politely', $s === 422, "status=$s");

section('UAT — staff cannot shop from an admin account');
[$s, , $loc] = req($kernel, $app, 'GET', '/account', [], $admin);
ok('admin opening My Account is sent to the dashboard', $s === 302 && str_ends_with((string) $loc, '/dashboard'), "status=$s");
[$s] = req($kernel, $app, 'POST', '/cart/add', ['_token' => $formToken, 'product_model_id' => 1, 'quantity' => 1], $admin, true);
ok('admin cannot add to cart', $s === 403, "status=$s");
[, $h] = req($kernel, $app, 'GET', '/', [], $admin);
ok('admin sees "Admin Panel" instead of "My Account"', str_contains($h, 'Admin Panel') && ! str_contains($h, 'My Account'));
[, $h] = req($kernel, $app, 'GET', '/', [], $customer);
ok('a customer still sees My Account and the cart', str_contains($h, 'My Account') && str_contains($h, 'aria-label="Cart"'));

section('UAT — owner changes the phone number in admin');
$token = $formToken;
$base = ['_token' => $token, 'business_name' => 'AutoBazaar', 'review_login_enabled' => 'on',
         'review_driver_phone' => '9094262603', 'review_customer_phone' => '8939345008', 'review_otp' => '2203'];
req($kernel, $app, 'POST', '/settings/general-settings/update', $base + ['business_mobile' => '9876543210'], $super);
[, $h] = req($kernel, $app, 'GET', '/');
ok('the new number appears on the website at once', str_contains($h, '98765 43210') && str_contains($h, 'wa.me/919876543210'));
req($kernel, $app, 'POST', '/settings/general-settings/update', $base + ['business_mobile' => ''], $super);
[, $h] = req($kernel, $app, 'GET', '/');
ok('clearing it brings back the original number', str_contains($h, '86088 60893'));

section('UAT — app store links and QR');
[, $h] = req($kernel, $app, 'GET', '/app');
ok('Google Play badge opens the real listing', substr_count($h, 'com.jpautozone.app') >= 2);
ok('iPhone shows Coming soon, not a dead link', str_contains($h, 'Coming soon') || str_contains($h, 'data-ab-soon'));
ok('the footer QR is the real scannable file', str_contains($h, 'app-qr-play.svg'));

/* =============================================================== DESIGN */
section('DESIGN — look and feel');
[, $home] = req($kernel, $app, 'GET', '/');
ok('site uses the chosen font (Poppins)', str_contains($home, 'family=Poppins') && str_contains($home, "--font-sans: 'Poppins'"));
ok('buttons have the hover glow', str_contains($home, 'Button glow'));
ok('menu is shortened with a More dropdown', str_contains($home, 'ab-more-btn'));
ok('footer credit reads Crafted by Ziga Infotech', str_contains($home, 'Crafted by') && str_contains($home, '>Ziga Infotech</a>'));
ok('the footer browse rows stay hidden (owner asked)', ! str_contains($home, 'Popular Brands'));
ok('page loader is on every page', str_contains($home, 'id="ab-loader"'));
ok('fuel chips and price highlight are shown', str_contains($home, 'ab-fuel ab-fuel--') && str_contains($home, 'ab-price'));
ok('one phone number only, no second line', substr_count($home, '63840 88408') === 0);
ok('reduced-motion is respected everywhere', substr_count($home, 'prefers-reduced-motion') >= 3, substr_count($home, 'prefers-reduced-motion') . ' blocks');

section('DESIGN — every public page has its SEO tags');
$missing = [];
foreach (['/', '/used-autos', '/new-autos/tvs/king-deluxe', '/compare', '/offers', '/contact', '/faq', '/accessories/shop', '/used-autos/' . $listings[0]['id'] . '/' . $listings[0]['slug']] as $p) {
    [, $h] = req($kernel, $app, 'GET', $p);
    $gaps = [];
    if (! preg_match('/<title>[^<]{10,}<\/title>/', $h)) $gaps[] = 'title';
    if (! preg_match('/<meta name="description" content="[^"]{60,}"/', $h)) $gaps[] = 'description';
    if (! str_contains($h, 'rel="canonical"')) $gaps[] = 'canonical';
    if (! str_contains($h, 'og:image')) $gaps[] = 'share image';
    if (substr_count($h, '<h1') !== 1) $gaps[] = 'h1=' . substr_count($h, '<h1');
    preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $h, $ld);
    foreach ($ld[1] as $j) { if (! is_array(json_decode($j, true))) { $gaps[] = 'broken data'; break; } }
    if ($gaps) { $missing[] = $p . ' (' . implode(', ', $gaps) . ')'; }
}
ok('9 sample pages carry title, description, canonical, share image, one h1 and valid data', ! $missing, $missing ? implode(' | ', $missing) : 'all complete');
[, $x] = req($kernel, $app, 'GET', '/sitemap.xml');
$sx = simplexml_load_string($x);
ok('sitemap is valid and lists the whole site', $sx && count($sx->url) >= 70, ($sx ? count($sx->url) : 0) . ' pages');

section('DESIGN — pages do not leak code or errors');
$leaks = [];
foreach (['/', '/used-autos', '/compare', '/accessories/shop', '/finance-emi', '/contact'] as $p) {
    [, $h] = req($kernel, $app, 'GET', $p);
    foreach (['Warning</b>', 'Undefined ', 'Stack trace', 'SQLSTATE', '&amp;amp;'] as $needle) {
        if (str_contains($h, $needle)) { $leaks[] = "$p shows '$needle'"; }
    }
}
ok('no warnings, no leftover code, no double-escaped text', ! $leaks, $leaks ? implode(' | ', $leaks) : 'clean');

section('SECURITY — the earlier fixes still hold');
$t = $customer->createToken('suite'); $plain = $t->plainTextToken;
[$s] = req($kernel, $app, 'GET', '/api/get-profile', [], null, true);
ok('API needs a token', $s === 401);
Carbon::setTestNow(now()->addDays(95));
$r = Illuminate\Http\Request::create('/api/get-profile', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $plain]);
$app['auth']->forgetGuards(); $app->instance('request', $r);
ok('a token unused for 95 days stops working', $kernel->handle($r)->getStatusCode() === 401);
Carbon::setTestNow();
DB::table('personal_access_tokens')->where('id', $t->accessToken->id)->delete();
$codes = [];
for ($i = 0; $i < 11; $i++) { [$c] = req($kernel, $app, 'POST', '/api/auth/user-verifyOtp', ['phone' => '8888888888', 'otp' => '0000', 'fcm_token' => 'x', 'device_id' => 'x'], null, true); $codes[] = $c; }
ok('OTP guessing is blocked after 10 tries', $codes[10] === 429, 'last=' . $codes[10]);
[, $b] = req($kernel, $app, 'GET', '/api/get-sold-auto', [], null, true);
ok('API errors never show database details', ! str_contains((string) $b, 'SQLSTATE'));

} finally {
    Carbon::setTestNow();
    DB::table('settings')->delete();
    foreach ($snapshot as $row) { DB::table('settings')->insert($row); }
    Setting::flushCache(); SiteData::flush(); Cache::flush();
    echo "\n(settings restored: " . DB::table('settings')->count() . " rows)\n";
}

echo "\n==================== RESULT: $pass passed, $fail failed\n";
foreach ($failed as $f) { echo "   FAILED: $f\n"; }
