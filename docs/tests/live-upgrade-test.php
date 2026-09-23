<?php
/**
 * Rehearses the live deploy.
 *
 * Takes the structure of the database you are running on now — every table,
 * plus the record of which migrations have already been applied — copies it
 * into a scratch database, and runs `php artisan migrate` against that copy.
 *
 * Then it checks the only thing that matters: that the upgrade changed nothing.
 * The new migrations should all record themselves as done and leave every
 * existing table exactly as it was.
 *
 * Your real database is only read from. Run this before every deploy.
 *
 *     php docs/tests/live-upgrade-test.php
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '1024M');
set_time_limit(0);
require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$scratch = 'autobazaar_livecopy';
$mysqldump = 'C:/xampp/mysql/bin/mysqldump.exe';
$mysql = 'C:/xampp/mysql/bin/mysql.exe';

if (($argv[1] ?? '') !== '--checks') {
    $app = require 'bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

    $live = DB::getDatabaseName();
    if ($scratch === $live) { exit("Refusing to run: the scratch name is the live database.\n"); }

    $user = config('database.connections.mysql.username');
    $pass = (string) config('database.connections.mysql.password');
    $host = config('database.connections.mysql.host');
    $auth = '-h' . $host . ' -u' . $user . ($pass !== '' ? ' -p' . $pass : '');

    echo "copying the structure of `{$live}` into `{$scratch}`…\n";
    DB::statement("DROP DATABASE IF EXISTS `{$scratch}`");
    DB::statement("CREATE DATABASE `{$scratch}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    /*
     * The rows come too, not just the structure.
     *
     * An earlier version copied structure only, and that quietly hid a real
     * problem: the migration adding "one SOS per ride" cannot be applied while
     * repeats already exist, and with no rows in the copy there was nothing to
     * clash with. A deploy rehearsal that leaves the data out does not rehearse
     * the deploy.
     */
    $dump = sys_get_temp_dir() . '/ab-livecopy.sql';
    exec(escapeshellarg($mysqldump) . " {$auth} --routines --skip-add-locks {$live} > " . escapeshellarg($dump), $o, $e1);
    exec(escapeshellarg($mysql) . " {$auth} {$scratch} < " . escapeshellarg($dump), $o, $e2);

    if ($e1 || $e2) { exit("Could not copy the database (mysqldump exit {$e1}/{$e2}).\n"); }

    // remember the structure before the upgrade
    $before = snapshot($scratch);
    file_put_contents(sys_get_temp_dir() . '/ab-before.json', json_encode($before));

    echo "already applied there: " . DB::table('migrations')->count() . " migrations\n\n";
    echo "running `php artisan migrate` against the copy…\n\n";

    putenv('DB_DATABASE=' . $scratch);
    passthru('php artisan migrate --force --no-interaction', $migrateExit);

    passthru('php ' . escapeshellarg(__FILE__) . ' --checks ' . (int) $migrateExit, $exit);
    @unlink($dump);
    exit($exit);
}

/* ------------------------------------------------------------- the checks */
$migrateExit = (int) ($argv[2] ?? 1);

$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$pass = 0; $fail = 0;
function ok($label, $cond, $extra = '') {
    global $pass, $fail;
    $cond ? $pass++ : $fail++;
    echo ($cond ? '  PASS  ' : '  FAIL  ') . str_pad($label, 54) . ($extra !== '' ? "[$extra]" : '') . "\n";
}

function snapshot(string $db): array {
    $out = [];
    foreach (DB::select(
        'SELECT TABLE_NAME t, COLUMN_NAME c, COLUMN_TYPE ct, IS_NULLABLE n, COLUMN_DEFAULT d
         FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? ORDER BY TABLE_NAME, COLUMN_NAME',
        [$db]
    ) as $r) {
        $out[$r->t . '.' . $r->c] = $r->ct . '|' . $r->n . '|' . ($r->d ?? '~');
    }

    return $out;
}

echo "\n";
ok('migrate finishes without an error', $migrateExit === 0, "exit={$migrateExit}");

$before = json_decode(file_get_contents(sys_get_temp_dir() . '/ab-before.json'), true);
$after = snapshot(DB::getDatabaseName());

$changed = [];
foreach ($before as $key => $spec) {
    if (! isset($after[$key])) { $changed[] = "{$key} was removed"; }
    elseif ($after[$key] !== $spec) { $changed[] = "{$key} changed: [{$spec}] -> [{$after[$key]}]"; }
}
$addedColumns = array_diff(array_keys($after), array_keys($before));

ok('no existing column was changed or removed', ! $changed, $changed ? count($changed) . ' changed' : count($before) . ' columns untouched');
ok('no column was added to an existing table', ! $addedColumns, $addedColumns ? implode(', ', array_slice($addedColumns, 0, 5)) : 'none added');

if ($changed) {
    echo "\n  changes:\n";
    foreach (array_slice($changed, 0, 20) as $c) { echo "    - {$c}\n"; }
}

$pending = 0;
foreach (glob('database/migrations/*.php') as $f) {
    if (! DB::table('migrations')->where('migration', basename($f, '.php'))->exists()) { $pending++; }
}
ok('every migration is now recorded as run', $pending === 0, $pending . ' still pending');
ok('the rows are all still there', DB::table('users')->count() > 1000,
   number_format(DB::table('users')->count()) . ' users, ' . DB::table('migrations')->count() . ' migrations recorded');

echo "\n==================== RESULT: {$pass} passed, {$fail} failed\n";
echo "If this passes, `php artisan migrate` is safe to run on the live server.\n";
exit($fail ? 1 : 0);
