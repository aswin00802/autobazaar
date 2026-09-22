<?php
/**
 * Does a clone of this repository actually run?
 *
 * Builds a brand new database from nothing with migrate + seed, exactly as
 * somebody setting up a new machine would, then points the whole site at it and
 * checks the things a new person would try first: the home page opens, the
 * admin can sign in, the dropdowns have options in them, and registration can
 * find an area.
 *
 * The live database is never touched. The scratch one is dropped and rebuilt.
 *
 *     php docs/tests/fresh-install-test.php
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '1024M');
set_time_limit(0);
require 'vendor/autoload.php';

$scratch = 'autobazaar_fresh';

use Illuminate\Support\Facades\DB;

/*
 * Two passes, because a process that has already booted against the live
 * database keeps reading it however the settings are changed afterwards.
 *
 *   first  — create the database, migrate and seed it
 *   second — this same file again, started with DB_DATABASE pointing at the new
 *            one, so the checks below really are the new install answering
 */
if (($argv[1] ?? '') !== '--checks') {
    $boot = require 'bootstrap/app.php';
    $boot->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    if ($scratch === DB::getDatabaseName()) { exit("Refusing to run: the scratch name is the live database.\n"); }

    echo "building `{$scratch}` from nothing…\n\n";
    DB::statement("DROP DATABASE IF EXISTS `{$scratch}`");
    DB::statement("CREATE DATABASE `{$scratch}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    putenv('DB_DATABASE=' . $scratch);

    $started = microtime(true);
    passthru('php artisan migrate --force --no-interaction', $migrateExit);
    echo "\n";
    passthru('php artisan db:seed --force --no-interaction', $seedExit);
    $seconds = microtime(true) - $started;

    passthru(
        'php ' . escapeshellarg(__FILE__) . ' --checks '
        . (int) $migrateExit . ' ' . (int) $seedExit . ' ' . (int) round($seconds),
        $exit
    );
    exit($exit);
}

$migrateExit = (int) ($argv[2] ?? 1);
$seedExit    = (int) ($argv[3] ?? 1);
$seconds     = (int) ($argv[4] ?? 0);

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$pass = 0; $fail = 0; $failed = [];
function ok($label, $cond, $extra = '') {
    global $pass, $fail, $failed;
    $cond ? $pass++ : ($fail++ + array_push($failed, $label));
    echo ($cond ? '  PASS  ' : '  FAIL  ') . str_pad($label, 52) . ($extra !== '' ? "[$extra]" : '') . "\n";
}
function section($t) { echo "\n== $t\n"; }

function get($uri, $user = null) {
    global $kernel, $app;
    $app['auth']->forgetGuards();
    $request = Illuminate\Http\Request::create($uri, 'GET');
    if ($user) { $app['auth']->guard('web')->setUser($user); }
    $app->instance('request', $request);
    $response = $kernel->handle($request);

    return [$response->getStatusCode(), $response->getContent()];
}

section('IT BUILDS');
ok('migrate runs on an empty database', $migrateExit === 0, "exit={$migrateExit}");
ok('seed runs straight after it', $seedExit === 0, sprintf('both in %.0fs', $seconds));
ok('and it really is the fresh database', DB::connection()->getDatabaseName() === $scratch,
   DB::connection()->getDatabaseName());

section('THE LISTS A NEW INSTALL NEEDS');
foreach ([
    'roles'                   => 3,
    'permissions'             => 129,
    'role_has_permissions'    => 108,
    'auto_brands'             => 8,
    'auto_models'             => 78,
    'auto_fuel_types'         => 5,
    'countries'               => 246,
    'states'                  => 4092,
    'cities'                  => 47941,
    'tbl_auto_areas'          => 16701,
    'tbl_auto_cities'         => 4,
] as $table => $expected) {
    $actual = DB::table($table)->count();
    ok(str_pad($table, 22) . ' has its rows', $actual === $expected, number_format($actual) . ' of ' . number_format($expected));
}

section('NOBODY REAL CAME ALONG FOR THE RIDE');
ok('only the one seeded admin, no real customers', DB::table('users')->count() === 1, DB::table('users')->count() . ' users');
foreach (['auto_posts', 'auto_enquiries', 'quotations', 'shop_orders', 'auto_otps', 'personal_access_tokens'] as $table) {
    ok(str_pad($table, 22) . ' is empty', DB::table($table)->count() === 0, DB::table($table)->count() . ' rows');
}

section('SOMEBODY CAN ACTUALLY SIGN IN');
$admin = App\Models\User::where('phone_number', Database\Seeders\AdminUserSeeder::PHONE)->first();
ok('the seeded admin exists', (bool) $admin, $admin ? 'id=' . $admin->id : 'missing');
ok('their password is the one in the README', $admin && Illuminate\Support\Facades\Hash::check(
    Database\Seeders\AdminUserSeeder::PASSWORD, $admin->password));
ok('they are a Super Admin', $admin && $admin->hasRole('Super Admin'));
ok('which lets them through to everything', $admin && $admin->can('dashboard'));

section('THE SITE OPENS');
foreach (['/', '/new-autos', '/used-autos', '/accessories/shop', '/contact', '/user/login', '/user/register'] as $uri) {
    [$status, $html] = get($uri);
    $broken = preg_match('/Fatal error|SQLSTATE|Undefined (variable|array key)/i', $html);
    ok(str_pad($uri, 22) . ' opens', $status === 200 && ! $broken, $broken ? 'shows an error' : "HTTP {$status}");
}

[$status, $html] = get('/user/register');
ok('sign-up lists the districts', substr_count($html, '<option value="') > 3, substr_count($html, '<option value="') . ' options');

[$status, $html] = get('/user/areas/' . DB::table('tbl_auto_cities')->value('id'));
$areas = json_decode($html, true);
ok('and a district finds its areas', ! empty($areas['areas']), count($areas['areas'] ?? []) . ' areas');

section('THE ADMIN PANEL OPENS');
foreach (['/dashboard', '/masters/auto-brands', '/masters/city', '/settings/general-settings', '/spare-parts/product'] as $uri) {
    [$status, $html] = get($uri, $admin);
    $broken = preg_match('/Fatal error|SQLSTATE|Undefined (variable|array key)/i', $html);
    ok(str_pad($uri, 22) . ' opens', $status === 200 && ! $broken, $broken ? 'shows an error' : "HTTP {$status}");
}

section('PAGES COPE WITH AN EMPTY CATALOGUE');
// A new install has no vehicle catalogue until it is seeded, and these two
// pages used to return a server error rather than simply showing less.
foreach (['/finance-emi', '/enquiry'] as $uri) {
    [$status, $html] = get($uri);
    $broken = preg_match('/Fatal error|SQLSTATE|Undefined (variable|array key)/i', $html);
    ok(str_pad($uri, 22) . ' opens with no catalogue', $status === 200 && ! $broken, "HTTP {$status}");
}
ok('the uploads folder is kept in the repository', is_file('public/uploads/.gitkeep'));

echo "\n==================== RESULT: {$pass} passed, {$fail} failed\n";
if ($failed) { echo 'failed: ' . implode(' | ', $failed) . "\n"; }
echo "the scratch database `{$scratch}` was left in place so you can look around it.\n";
exit($fail ? 1 : 0);
