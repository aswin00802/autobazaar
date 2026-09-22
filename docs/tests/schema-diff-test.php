<?php
/**
 * Proves a fresh install matches the real database.
 *
 * Builds a brand new database from nothing using only `php artisan migrate`,
 * then compares it table by table and column by column against the database
 * the site is actually running on, and reports every difference.
 *
 * The live database is only ever read from. The scratch database is dropped
 * and rebuilt each run.
 *
 *     php docs/tests/schema-diff-test.php
 *
 * The scratch database name can be changed at the top if 'autobazaar_fresh'
 * is taken.
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '1024M');
set_time_limit(0);
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$scratch = 'autobazaar_fresh';
$live = DB::getDatabaseName();

if ($scratch === $live) {
    exit("Refusing to run: the scratch name is the live database.\n");
}

// Tables we deliberately do not ship a migration for.
$ignore = ['migrations', 'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring', 'tbl_auto_areas_old'];

/*
 * Two differences are known, understood and left alone. Both were checked by
 * hand; neither stops a fresh install working.
 *
 *  - fairprice_fare_settings.payment_mode was added straight to the database by
 *    hand and no migration ever created it. Nothing in the code reads it, so a
 *    new install simply does not have it rather than carrying the leftover on.
 *
 *  - sos_alerts_ride_id_foreign: here the fresh database is the correct one.
 *    The migration adding the unique key on (ride_id, triggered_by) has never
 *    been run on this database, and once it is, MySQL drops that now-redundant
 *    index by itself, exactly as it already does on a fresh install.
 */
$expectedColumnDifferences = ['fairprice_fare_settings.payment_mode missing'];
$expectedIndexDifferences = ['sos_alerts: index sos_alerts_ride_id_foreign missing'];

$pass = 0; $fail = 0;
function ok($label, $cond, $extra = '') {
    global $pass, $fail;
    $cond ? $pass++ : $fail++;
    echo ($cond ? '  PASS  ' : '  FAIL  ') . str_pad($label, 50) . ($extra !== '' ? "[$extra]" : '') . "\n";
}

echo "live database    : {$live}\n";
echo "scratch database : {$scratch}\n";

/* ------------------------------------------------ build the fresh database */
DB::statement("DROP DATABASE IF EXISTS `{$scratch}`");
DB::statement("CREATE DATABASE `{$scratch}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

// artisan runs as a process of its own, so the scratch database is handed over
// through the environment. Laravel never lets .env overwrite a real environment
// variable, so this wins for the child and leaves this process alone.
putenv('DB_DATABASE=' . $scratch);

echo "\nrunning migrations on the empty database…\n";
$started = microtime(true);
$exit = 0;
$output = [];
exec('php artisan migrate --force 2>&1', $output, $exit);
putenv('DB_DATABASE');
$seconds = microtime(true) - $started;

$ran = 0;
foreach ($output as $line) { if (str_contains($line, 'DONE')) { $ran++; } }

ok('every migration runs on an empty database', $exit === 0, sprintf('%d migrations, %.1fs', $ran, $seconds));
if ($exit !== 0) {
    echo "\n" . implode("\n", array_slice($output, -25)) . "\n";
    exit(1);
}

/* -------------------------------------------------------------- compare it */
function tablesOf(string $db): array {
    $rows = DB::select('SELECT TABLE_NAME t FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?', [$db]);
    $names = array_map(fn ($r) => $r->t, $rows);
    sort($names);

    return $names;
}

function columnsOf(string $db, string $table): array {
    $out = [];
    foreach (DB::select(
        'SELECT COLUMN_NAME c, COLUMN_TYPE ct, IS_NULLABLE n, COLUMN_DEFAULT d, EXTRA e
         FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY COLUMN_NAME',
        [$db, $table]
    ) as $r) {
        // Laravel writes DEFAULT NULL where the dump left it implicit; treat as the same.
        $default = $r->d === null ? '~' : strtoupper((string) $r->d);
        $out[$r->c] = strtolower($r->ct) . '|' . $r->n . '|' . $default . '|' . strtolower((string) $r->e);
    }

    return $out;
}

$liveTables = array_diff(tablesOf($live), $ignore);
$freshTables = array_diff(tablesOf($scratch), $ignore);

$missing = array_diff($liveTables, $freshTables);
$extra = array_diff($freshTables, $liveTables);

ok('a fresh install has every table the live site has', ! $missing,
   $missing ? 'missing: ' . implode(', ', $missing) : count($liveTables) . ' tables');
ok('and no tables the live site does not have', ! $extra,
   $extra ? 'extra: ' . implode(', ', $extra) : 'none extra');

$columnProblems = [];
$checked = 0;

foreach (array_intersect($liveTables, $freshTables) as $table) {
    $a = columnsOf($live, $table);
    $b = columnsOf($scratch, $table);
    $checked += count($a);

    foreach ($a as $col => $spec) {
        if (! isset($b[$col])) { $columnProblems[] = "{$table}.{$col} missing"; continue; }
        if ($b[$col] !== $spec) { $columnProblems[] = "{$table}.{$col}: live [{$spec}] vs fresh [{$b[$col]}]"; }
    }
    foreach ($b as $col => $spec) {
        if (! isset($a[$col])) { $columnProblems[] = "{$table}.{$col} is extra"; }
    }
}

$columnProblems = array_values(array_diff($columnProblems, $expectedColumnDifferences));

ok('every column matches in type, null and default', ! $columnProblems,
   $columnProblems ? count($columnProblems) . ' differences' : number_format($checked) . ' columns checked, 1 known difference allowed');

if ($columnProblems) {
    echo "\n  column differences:\n";
    foreach (array_slice($columnProblems, 0, 40) as $p) { echo "    - {$p}\n"; }
    if (count($columnProblems) > 40) { echo '    … and ' . (count($columnProblems) - 40) . " more\n"; }
}

/* ------------------------------------------------------------------ indexes */
function indexesOf(string $db, string $table): array {
    $out = [];
    foreach (DB::select(
        'SELECT INDEX_NAME i, NON_UNIQUE nu, SEQ_IN_INDEX s, COLUMN_NAME c
         FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?
         ORDER BY INDEX_NAME, SEQ_IN_INDEX',
        [$db, $table]
    ) as $r) {
        $out[$r->i]['unique'] = ((int) $r->nu) === 0;
        $out[$r->i]['cols'][] = $r->c;
    }
    ksort($out);

    return $out;
}

$indexProblems = [];
foreach (array_intersect($liveTables, $freshTables) as $table) {
    $a = indexesOf($live, $table);
    $b = indexesOf($scratch, $table);

    foreach ($a as $name => $def) {
        if (! isset($b[$name])) { $indexProblems[] = "{$table}: index {$name} missing"; continue; }
        if ($b[$name]['cols'] !== $def['cols'] || $b[$name]['unique'] !== $def['unique']) {
            $indexProblems[] = "{$table}: index {$name} differs (" . implode(',', $def['cols']) . ')';
        }
    }
}

$indexProblems = array_values(array_diff($indexProblems, $expectedIndexDifferences));

ok('every index the live site has also exists', ! $indexProblems,
   $indexProblems ? count($indexProblems) . ' differences' : 'all present, 1 known difference allowed');

if ($indexProblems) {
    echo "\n  index differences:\n";
    foreach (array_slice($indexProblems, 0, 25) as $p) { echo "    - {$p}\n"; }
    if (count($indexProblems) > 25) { echo '    … and ' . (count($indexProblems) - 25) . " more\n"; }
}

echo "\n==================== RESULT: {$pass} passed, {$fail} failed\n";
echo "the scratch database `{$scratch}` was left in place so you can look at it.\n";
exit($fail ? 1 : 0);
