<?php
/**
 * Which uploaded files does the database point at, and which are not here?
 *
 * Scans every text column in the database for paths that look like uploads,
 * then checks each one against public/. What comes back missing is exactly what
 * needs copying off the old server.
 *
 * Read-only.
 *
 *     php docs/tests/missing-uploads-report.php
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '1024M');
set_time_limit(0);
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$db = DB::getDatabaseName();
echo "reading `{$db}`\n\n";

$columns = DB::select(
    "SELECT TABLE_NAME t, COLUMN_NAME c FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = ? AND DATA_TYPE IN ('varchar','text','mediumtext','longtext')
     ORDER BY TABLE_NAME, COLUMN_NAME",
    [$db]
);

$found = [];      // path => [tables it appears in]

foreach ($columns as $col) {
    try {
        $values = DB::table($col->t)
            ->whereNotNull($col->c)
            ->where($col->c, 'like', '%uploads/%')
            ->limit(20000)
            ->pluck($col->c);
    } catch (Throwable $e) {
        continue;
    }

    foreach ($values as $value) {
        if (! preg_match_all('#(?:^|["\'\s,(])((?:public/)?uploads/[A-Za-z0-9_./-]+\.[A-Za-z0-9]{2,5})#', (string) $value, $m)) {
            continue;
        }
        foreach ($m[1] as $path) {
            $path = ltrim(str_replace('public/', '', $path), '/');
            $found[$path][$col->t . '.' . $col->c] = true;
        }
    }
}

ksort($found);

$here = [];
$gone = [];
foreach ($found as $path => $where) {
    if (is_file(public_path($path))) {
        $here[$path] = array_keys($where);
    } else {
        $gone[$path] = array_keys($where);
    }
}

printf("%s paths referenced by the database\n", number_format(count($found)));
printf("  %s already here\n", number_format(count($here)));
printf("  %s NOT here — these are the ones to copy from the old server\n\n", number_format(count($gone)));

if (! $gone) {
    echo "Nothing is missing. The zip carries everything the database points at.\n";
    exit(0);
}

// group the missing ones by folder, which is what you actually copy
$folders = [];
foreach ($gone as $path => $where) {
    $folder = dirname($path);
    $folders[$folder]['count'] = ($folders[$folder]['count'] ?? 0) + 1;
    $folders[$folder]['used_by'] = array_unique(array_merge($folders[$folder]['used_by'] ?? [], $where));
}
ksort($folders);

echo "MISSING, BY FOLDER\n";
echo str_repeat('-', 78) . "\n";
foreach ($folders as $folder => $info) {
    printf("  %-34s %5s file%s   used by: %s\n", $folder . '/', number_format($info['count']),
        $info['count'] === 1 ? ' ' : 's', implode(', ', array_slice($info['used_by'], 0, 3)));
}

$list = 'docs/uploads-to-copy.txt';
file_put_contents($list, implode("\n", array_keys($gone)) . "\n");

echo str_repeat('-', 78) . "\n";
echo "\nThe full list of " . number_format(count($gone)) . " files is written to {$list}\n";
echo "Copy these from the OLD server's public/ folder into the new server's public/ folder,\n";
echo "keeping the same folder names.\n";
