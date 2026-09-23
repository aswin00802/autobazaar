<?php
/**
 * Rehearses the deploy you actually plan to do.
 *
 *   1. take a copy of the old live database (auto_bazar), data and all
 *   2. run every file in docs/sql against it, in order
 *   3. check the result has everything the code expects
 *   4. point the whole site at it and open the pages that matter
 *
 * Nothing you are working on is touched. The copy is dropped and rebuilt each
 * run, and the old database is only read from.
 *
 *     php docs/tests/deploy-sql-test.php
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '1024M');
set_time_limit(0);
require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$oldLive = 'auto_bazar';        // the database the live server is running now
$copy    = 'ab_deploytest';     // where we rehearse
$current = 'autobazaar';        // what the code is developed against — the target

$mysqldump = 'C:/xampp/mysql/bin/mysqldump.exe';
$mysql     = 'C:/xampp/mysql/bin/mysql.exe';

$sqlFiles = [
    'docs/sql/01_ecommerce_tables.sql',
    'docs/sql/02_migrate_old_app_orders.sql',
    'docs/sql/03_vehicle_catalog_tables.sql',
    'docs/sql/04_vehicle_catalog_permissions.sql',
    'docs/sql/05_performance_indexes.sql',
    'docs/sql/06_catalogue_content.sql',
    'docs/sql/07_activity_and_notifications.sql',
];

if (($argv[1] ?? '') !== '--checks') {
    $app = require 'bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    if (in_array($copy, [$oldLive, $current, DB::getDatabaseName()], true)) {
        exit("Refusing to run: the scratch name collides with a real database.\n");
    }

    $user = config('database.connections.mysql.username');
    $pass = (string) config('database.connections.mysql.password');
    $host = config('database.connections.mysql.host');
    $auth = '-h' . $host . ' -u' . $user . ($pass !== '' ? ' -p' . $pass : '');

    echo "1. copying `{$oldLive}` (the old live database) into `{$copy}`…\n";
    DB::statement("DROP DATABASE IF EXISTS `{$copy}`");
    DB::statement("CREATE DATABASE `{$copy}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $dump = sys_get_temp_dir() . '/ab-oldlive.sql';
    exec(escapeshellarg($mysqldump) . " {$auth} --routines --skip-add-locks {$oldLive} > " . escapeshellarg($dump), $o, $e);
    if ($e) { exit("could not dump {$oldLive} (exit {$e})\n"); }
    exec(escapeshellarg($mysql) . " {$auth} {$copy} < " . escapeshellarg($dump), $o, $e);
    if ($e) { exit("could not load the copy (exit {$e})\n"); }
    @unlink($dump);

    $before = (int) DB::selectOne('SELECT COUNT(*) n FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?', [$copy])->n;
    echo "   copied: {$before} tables\n\n";

    echo "2. running the SQL files…\n";
    $results = [];
    foreach ($sqlFiles as $file) {
        $started = microtime(true);
        exec(escapeshellarg($mysql) . " {$auth} {$copy} < " . escapeshellarg($file) . ' 2>&1', $out, $code);
        $results[] = [basename($file), $code, microtime(true) - $started, implode(' ', array_slice($out, 0, 2))];
        printf("   %-38s %s  %.1fs\n", basename($file), $code === 0 ? 'ok  ' : 'FAILED', microtime(true) - $started);
        if ($code !== 0) { echo '      ' . implode("\n      ", array_slice($out, 0, 4)) . "\n"; }
        $out = [];
    }

    $after = (int) DB::selectOne('SELECT COUNT(*) n FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?', [$copy])->n;
    echo "   now: {$after} tables (was {$before})\n";

    file_put_contents(sys_get_temp_dir() . '/ab-deploy-results.json', json_encode([
        'results' => $results, 'before' => $before, 'after' => $after,
    ]));

    putenv('DB_DATABASE=' . $copy);
    passthru('php ' . escapeshellarg(__FILE__) . ' --checks', $exit);
    exit($exit);
}

/* ------------------------------------------------------------- the checks */
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$state = json_decode(file_get_contents(sys_get_temp_dir() . '/ab-deploy-results.json'), true);

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

echo "\n";
section('THE SQL FILES');
foreach ($state['results'] as [$name, $code, $seconds, $firstLines]) {
    ok(str_pad($name, 40) . ' runs', $code === 0, $code === 0 ? sprintf('%.1fs', $seconds) : $firstLines);
}
ok('the old database gained its missing tables', $state['after'] > $state['before'],
   $state['before'] . ' -> ' . $state['after'] . ' tables');

section('IT NOW HAS WHAT THE CODE EXPECTS');
ok('the site is pointed at the upgraded copy', DB::getDatabaseName() === 'ab_deploytest', DB::getDatabaseName());

$needed = [
    'shop_carts', 'shop_cart_items', 'shop_addresses', 'shop_orders', 'shop_order_items',
    'shop_coupons', 'shop_coupon_usages', 'shop_order_status_histories',
    'vehicle_variants', 'vehicle_reviews', 'finance_lender_rates',
];
$missing = [];
foreach ($needed as $table) {
    if (! Illuminate\Support\Facades\Schema::hasTable($table)) { $missing[] = $table; }
}
ok('every table the new features need is there', ! $missing, $missing ? implode(', ', $missing) : count($needed) . ' checked');

// compare against the database the code is developed against
$reference = 'autobazaar';
$refTables = array_map(fn ($r) => $r->t, DB::select('SELECT TABLE_NAME t FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?', [$reference]));
$copyTables = array_map(fn ($r) => $r->t, DB::select('SELECT TABLE_NAME t FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?', [DB::getDatabaseName()]));
$stillMissing = array_diff($refTables, $copyTables, ['telescope_entries', 'telescope_entries_tags', 'telescope_monitoring', 'tbl_auto_areas_old']);

ok('nothing the development database has is missing', ! $stillMissing,
   $stillMissing ? count($stillMissing) . ' missing: ' . implode(', ', array_slice($stillMissing, 0, 6)) : count($copyTables) . ' tables');

/*
 * Every column too, not just every table.
 *
 * Comparing table names alone let a real gap through: a column added to `users`
 * by a later migration was in no SQL file, so a deploy done this way would have
 * been missing it and the screen that reads it could not load. Anything added
 * from here on is caught by this without anyone having to remember.
 */
function columnsOf(string $db): array {
    $out = [];
    foreach (DB::select(
        'SELECT TABLE_NAME t, COLUMN_NAME c FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ?',
        [$db]
    ) as $r) {
        $out[$r->t . '.' . $r->c] = true;
    }

    return $out;
}

$ignoreTables = ['telescope_entries', 'telescope_entries_tags', 'telescope_monitoring', 'tbl_auto_areas_old'];
$devColumns = columnsOf($reference);
$copyColumns = columnsOf(DB::getDatabaseName());

$missingColumns = [];
foreach (array_keys($devColumns) as $key) {
    if (isset($copyColumns[$key])) { continue; }
    if (in_array(explode('.', $key)[0], $ignoreTables, true)) { continue; }
    $missingColumns[] = $key;
}

ok('and not a single column either', ! $missingColumns,
   $missingColumns
     ? count($missingColumns) . ' missing: ' . implode(', ', array_slice($missingColumns, 0, 6))
     : number_format(count($devColumns)) . ' columns checked');

section('THE SITE RUNS ON IT');
foreach (['/', '/new-autos', '/used-autos', '/accessories/shop', '/finance-emi', '/contact', '/user/login'] as $uri) {
    [$status, $html] = get($uri);
    $broken = preg_match('/Fatal error|SQLSTATE|Undefined (variable|array key)/i', $html);
    ok(str_pad($uri, 22) . ' opens', $status === 200 && ! $broken, $broken ? 'shows an error' : "HTTP {$status}");
}

section('THE ADMIN RUNS ON IT');
$admin = App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))->first();
ok('there is still a Super Admin to sign in as', (bool) $admin, $admin ? $admin->phone_number : 'none found');

if ($admin) {
    foreach (['/dashboard', '/ecommerce/orders', '/vehicles/catalogue', '/masters/city', '/settings/general-settings'] as $uri) {
        [$status, $html] = get($uri, $admin);
        $broken = preg_match('/Fatal error|SQLSTATE|Undefined (variable|array key)/i', $html);
        ok(str_pad($uri, 22) . ' opens', $status === 200 && ! $broken, $broken ? 'shows an error' : "HTTP {$status}");
    }
}

section('THE CATALOGUE IS FILLED IN — NO SEEDERS NEEDED');
foreach (['vehicle_models' => 78, 'vehicle_variants' => 15, 'vehicle_reviews' => 18, 'finance_lender_rates' => 5, 'vehicle_specifications' => 66] as $table => $expected) {
    $n = DB::table($table)->count();
    ok(str_pad($table, 24) . ' has its rows', $n >= $expected, $n . ' of ' . $expected);
}
[$s2, $h2] = get('/new-autos');
ok('New Autos actually lists autos', substr_count($h2, 'ab-card') > 5, substr_count($h2, 'ab-card') . ' cards');
// Finance Options lives on a model page, not on /finance-emi.
$catalogue = app(App\Services\VehicleCatalogService::class)->all();
$modelUrl = $catalogue ? '/new-autos/' . $catalogue[0]['brand_slug'] . '/' . $catalogue[0]['model_slug'] : '/finance-emi';
[$s3, $h3] = get($modelUrl);
$lenderNames = DB::table('finance_lender_rates')->join('auto_financiar', 'auto_financiar.id', '=', 'finance_lender_rates.auto_financiar_id')->pluck('finance_name');
$shown = 0;
foreach ($lenderNames as $n) { if ($n && str_contains($h3, $n)) { $shown++; } }
ok('Finance Options lists the lenders', $shown > 0, $shown . ' of ' . count($lenderNames) . ' lender names on the page');

section('THE REAL DATA SURVIVED');
foreach (['users', 'auto_posts', 'auto_enquiries', 'quotations'] as $table) {
    $n = DB::table($table)->count();
    ok(str_pad($table, 22) . ' still has its rows', $n > 0, number_format($n) . ' rows');
}

echo "\n==================== RESULT: {$pass} passed, {$fail} failed\n";
if ($failed) { echo 'failed: ' . implode(' | ', $failed) . "\n"; }
echo "`ab_deploytest` was left in place so you can look at it.\n";
exit($fail ? 1 : 0);
