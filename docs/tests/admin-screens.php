<?php
/**
 * Opens every screen in the admin panel as a Super Admin and reports how many
 * rows each one is showing.
 *
 * Nothing is changed: every request is a GET.
 *
 * Run it on its own:
 *     php docs/tests/admin-screens.php
 *
 * full-flow-test.php also runs it, as a separate process, because a browser
 * likewise gives every page request a process of its own.
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '1024M');
set_time_limit(0);
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Setting;
use App\Models\User;
use App\Support\SiteData;

$app['session']->start();
$super = User::whereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))->first();

if (! $super) {
    echo "No Super Admin account found — nothing to check.\n";
    exit(1);
}

$screens = [
    'Dashboard'              => '/dashboard',
    'Needs attention'        => '/dashboard/attention',
    'Users list'             => '/user-management/users-list',
    'Autos posted by users'  => '/user-management/users-post-auto-list',
    'New autos'              => '/auto-management/new-auto',
    'Used autos'             => '/auto-management/used-auto',
    'Private / cargo autos'  => '/auto-management/private-cargo-auto',
    'Bajaj refinance autos'  => '/auto-management/bajaj-refinance-auto',
    'Sold autos'             => '/sold-auto-list',
    'Enquiries'              => '/enquiry-auto-list',
    'Quotation requests'     => '/quotation-list',
    'Driver requests'        => '/driver-request-list',
    'Emergency requests'     => '/emergency-request-list',
    'Shop orders'            => '/ecommerce/orders',
    'Coupons'                => '/ecommerce/coupons',
    'App orders (pending)'   => '/spare-parts/orders/pending',
    'App orders (completed)' => '/spare-parts/orders/success',
    'App orders (cancelled)' => '/spare-parts/orders/cancel',
    'Products'               => '/spare-parts/product',
    'Product categories'     => '/spare-parts/categories',
    'Product sub-categories' => '/spare-parts/sub-categories',
    'FairPrice rides'        => '/fairprice/rides',
    'FairPrice fares'        => '/fairprice/fare-settings',
    'Vehicle catalogue'      => '/vehicles/catalogue',
    'Vehicle leads'          => '/vehicles/leads',
    'Vehicle reviews'        => '/vehicles/reviews',
    'Auto meter'             => '/auto-meter',
    'Events'                 => '/events',
    'POS quotations'         => '/pos-quotation',
    'Brands'                 => '/masters/auto-brands',
    'Models'                 => '/masters/auto-brands/model',
    'Fuel types'             => '/masters/auto-fueltype',
    'Sellers'                => '/masters/auto-seller',
    'Authorized sellers'     => '/masters/auto-authorized-seller',
    'Finance companies'      => '/masters/auto-finance',
    'Cities'                 => '/masters/city',
    'States'                 => '/masters/state',
    'Countries'              => '/masters/country',
    'Gas stations'           => '/services/gas-station',
    'Mechanics'              => '/services/mechanic',
    'Insurance'              => '/services/insurance',
    'Re-finance'             => '/services/re-finance',
    'RTO'                    => '/services/rto',
    'Roles'                  => '/roles',
    'Permissions'            => '/permissions',
    'Role permissions'       => '/role_has_permission',
    'General settings'       => '/settings/general-settings',
    'Payment settings'       => '/settings/payment-settings',
    'SMTP settings'          => '/settings/smtp-settings',
    'Email templates'        => '/settings/email-template-settings',
];

printf("%-24s %-38s %8s %9s\n", 'SCREEN', 'ADDRESS', 'ROWS', 'OPENS IN');
echo str_repeat('-', 84) . "\n";

$broken = [];
$slow = [];

foreach ($screens as $name => $uri) {
    $app['auth']->forgetGuards();
    SiteData::flush();
    Setting::flushCache();
    $request = Illuminate\Http\Request::create($uri, 'GET');
    $app['auth']->guard('web')->setUser($super);
    $app->instance('request', $request);

    $started = microtime(true);
    $response = $kernel->handle($request);
    $seconds = microtime(true) - $started;

    $status = $response->getStatusCode();
    $html = $response->getContent();
    $rows = max(0, preg_match_all('/<tr[\s>]/', $html) - 1);      // minus the heading row
    $leak = preg_match('/Fatal error|Undefined (variable|array key)|SQLSTATE|Stack trace/i', $html);

    if ($status !== 200 || $leak) {
        $broken[] = "$name ($uri): " . ($leak ? 'shows an error' : "HTTP $status");
    } elseif ($seconds > 3) {
        $slow[] = sprintf('%s (%.1fs)', $name, $seconds);
    }

    printf("%-24s %-38s %8s %8.2fs\n", $name, $uri,
        $status === 200 && ! $leak ? $rows : 'PROBLEM', $seconds);

    unset($response, $html);
    gc_collect_cycles();
}

echo str_repeat('-', 84) . "\n";

if ($broken) {
    echo "\n  FAIL  " . count($broken) . " screen(s) did not open cleanly:\n";
    foreach ($broken as $b) { echo "          $b\n"; }
} else {
    echo "\n  PASS  all " . count($screens) . " admin screens open cleanly and show their data\n";
}

if ($slow) {
    echo "  note  slow to open (worth paginating): " . implode(', ', $slow) . "\n";
}

exit($broken ? 1 : 0);
