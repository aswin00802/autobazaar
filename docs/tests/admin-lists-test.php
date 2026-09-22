<?php
/**
 * The three paged admin lists — City, State and Country.
 *
 * Checks that each one has its search box and page links and no Export button,
 * that the row numbers carry on from page to page, that a search keeps its
 * words when you turn the page, and that setting per_page to 0 in
 * config/admin_lists.php brings the old "everything in one page" behaviour back.
 *
 * Also checks that Users and Quotation Requests are back on the same footing as
 * every other admin list: one page, the usual table, its own search and export.
 *
 * Read-only: every request is a GET.
 *
 *     php docs/tests/admin-lists-test.php
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '2048M');
set_time_limit(0);
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;

$pass = 0; $fail = 0;
function ok($label, $cond, $extra = '') {
    global $pass, $fail;
    $cond ? $pass++ : $fail++;
    echo ($cond ? '  PASS  ' : '  FAIL  ') . str_pad($label, 58) . ($extra !== '' ? "[$extra]" : '') . "\n";
}

$app['session']->start();
$super = User::whereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))->first();

function get($uri) {
    global $kernel, $app, $super;
    $app['auth']->forgetGuards();
    $r = Illuminate\Http\Request::create($uri, 'GET');
    $app['auth']->guard('web')->setUser($super);
    $app->instance('request', $r);
    $res = $kernel->handle($r);

    return [$res->getStatusCode(), $res->getContent()];
}

/** The first number in the last <tbody> of the page. */
function firstRowNumber($html) {
    preg_match_all('#<tbody>(.*?)</tbody>#s', $html, $b);
    $body = $b[1][count($b[1]) - 1] ?? '';
    preg_match('#<td[^>]*>\s*(\d[\d,]*)\s*</td>#', $body, $m);

    return (int) str_replace(',', '', $m[1] ?? 0);
}

echo "\n== the three paged screens: search box, page links, no old table\n";
foreach ([
    'City'    => '/masters/city',
    'State'   => '/masters/state',
    'Country' => '/masters/country',
] as $name => $uri) {
    [$status, $html] = get($uri);
    $hasSearch = (bool) preg_match('#<input type="search" name="q"#', $html);
    $hasPager  = str_contains($html, 'pagination') && preg_match('#href="[^"]*page=2#', $html);
    $noTable   = ! preg_match('/class="[^"]*datatables-(basic|fixed1|fixed2)/', $html);
    $noExport  = ! str_contains($html, 'export=csv');
    ok("$name: search box, page links, no old table, no Export",
        $status === 200 && $hasSearch && $hasPager && $noTable && $noExport,
        "http=$status search=" . (int) $hasSearch . " pager=" . (int) $hasPager
        . " old_table_gone=" . (int) $noTable . " no_export=" . (int) $noExport);
}

echo "\n== Users and Quotations are back on the usual pattern\n";
foreach ([
    'Users list' => ['/user-management/users-list', 'datatables-fixed2', 4000],
    'Quotations' => ['/quotation-list', 'datatables-fixed1', 1000],
] as $name => [$uri, $tableClass, $atLeast]) {
    [$status, $html] = get($uri);
    $rows = max(0, preg_match_all('/<tr[\s>]/', $html) - 1);
    $hasTable = str_contains($html, $tableClass);
    $noPager = ! preg_match('#href="[^"]*page=2#', $html);
    ok("$name: every row in one page, with the table's own search and export",
        $status === 200 && $rows > $atLeast && $hasTable && $noPager,
        number_format($rows) . ' rows, table=' . (int) $hasTable . ', no_pager=' . (int) $noPager);
}

echo "\n== row numbers carry on from page to page\n";
foreach (['/masters/city' => 50, '/masters/state' => 50, '/masters/country' => 50] as $uri => $per) {
    [, $p1] = get($uri);
    [, $p2] = get($uri . '?page=2');
    [, $p3] = get($uri . '?page=3');
    ok(substr($uri, 0, 34) . ' pages 1/2/3 start at 1 / 51 / 101',
       firstRowNumber($p1) === 1 && firstRowNumber($p2) === $per + 1 && firstRowNumber($p3) === 2 * $per + 1,
       firstRowNumber($p1) . ' / ' . firstRowNumber($p2) . ' / ' . firstRowNumber($p3));
}

echo "\n== a search keeps its words when you turn the page\n";
[, $html] = get('/masters/city?q=tamil');
ok('page links keep ?q=tamil', (bool) preg_match('#href="[^"]*q=tamil[^"]*page=2#', html_entity_decode($html)));
[, $html] = get('/masters/city?q=tamil&page=3');
ok('page 3 of a search still only shows matches', substr_count($html, 'Tamil Nadu') >= 50, substr_count($html, 'Tamil Nadu') . ' matches');

echo "\n== nothing found reads like a sentence, not an empty box\n";
[, $html] = get('/masters/city?q=zzzznothing');
ok('city screen says no cities found', str_contains($html, 'No cities found'));
[, $html] = get('/masters/state?q=zzzznothing');
ok('state screen says no states found', str_contains($html, 'No states found'));
[, $html] = get('/masters/country?q=zzzznothing');
ok('country screen says no countries found', str_contains($html, 'No countries found'));

echo "\n== the undo switch brings the old behaviour back\n";
config(['admin_lists.per_page.city' => 0]);
[$status, $html] = get('/masters/city');
$rows = max(0, preg_match_all('/<tr[\s>]/', $html) - 1);
ok('per_page 0 prints every city again, with the old table', $status === 200 && $rows > 47000 && str_contains($html, 'datatables-basic'),
   number_format($rows) . ' rows, ' . round(strlen($html) / 1048576, 1) . ' MB');
config(['admin_lists.per_page.city' => 50]);
[, $html] = get('/masters/city');
ok('and setting it back to 50 pages it again', (max(0, preg_match_all('/<tr[\s>]/', $html) - 1)) === 50);

echo "\n==================== RESULT: $pass passed, $fail failed\n";
exit($fail ? 1 : 0);
