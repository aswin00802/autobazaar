<?php
/**
 * Downloads the uploaded pictures that only exist on the live server.
 *
 * The database points at 761 files that are not on this machine — lender logos,
 * auto photos, product photos, profile pictures. They were uploaded through the
 * admin panel on the live server, so that server is the only copy of them.
 * If it is ever lost, they are gone.
 *
 * This fetches each one over plain HTTP from the live site and puts it where
 * the database expects it, under public/.
 *
 *     php docs/tools/fetch-missing-uploads.php
 *     php docs/tools/fetch-missing-uploads.php https://some-other-address.com
 *
 * Run it again any time: anything already downloaded is skipped, so it picks up
 * where it left off.
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '512M');
set_time_limit(0);
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$base = rtrim($argv[1] ?? 'https://autobazaar.online', '/');

/* ------------------------------ work out what is referenced but not here */
$columns = DB::select(
    "SELECT TABLE_NAME t, COLUMN_NAME c FROM information_schema.COLUMNS
     WHERE TABLE_SCHEMA = ? AND DATA_TYPE IN ('varchar','text','mediumtext','longtext')",
    [DB::getDatabaseName()]
);

$wanted = [];
foreach ($columns as $col) {
    try {
        $values = DB::table($col->t)->whereNotNull($col->c)
            ->where($col->c, 'like', '%uploads/%')->limit(20000)->pluck($col->c);
    } catch (Throwable $e) {
        continue;
    }

    foreach ($values as $value) {
        if (! preg_match_all('#(?:^|["\'\s,(])((?:public/)?uploads/[A-Za-z0-9_./-]+\.[A-Za-z0-9]{2,5})#', (string) $value, $m)) {
            continue;
        }
        foreach ($m[1] as $path) {
            $path = ltrim(str_replace('public/', '', $path), '/');
            if (! is_file(public_path($path))) { $wanted[$path] = true; }
        }
    }
}

$wanted = array_keys($wanted);
sort($wanted);

if (! $wanted) {
    echo "Nothing missing — every picture the database points at is already here.\n";
    exit(0);
}

printf("%s files to fetch from %s\n\n", number_format(count($wanted)), $base);

/* ------------------------------------------------------------- fetch them */
$done = 0; $failed = []; $bytes = 0;
$perFolder = [];

foreach ($wanted as $i => $path) {
    $target = public_path($path);
    $folder = dirname($target);
    if (! is_dir($folder)) { mkdir($folder, 0775, true); }

    $handle = fopen($target, 'wb');
    $ch = curl_init($base . '/' . $path);
    curl_setopt_array($ch, [
        CURLOPT_FILE => $handle,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_USERAGENT => 'AutoBazaar backup copy',
    ]);
    curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $size = (int) curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
    curl_close($ch);
    fclose($handle);

    $dir = dirname($path);

    if ($status === 200 && $size > 0) {
        $done++;
        $bytes += $size;
        $perFolder[$dir]['ok'] = ($perFolder[$dir]['ok'] ?? 0) + 1;
    } else {
        @unlink($target);                 // do not leave an empty file behind
        $failed[] = $path . '  (HTTP ' . $status . ')';
        $perFolder[$dir]['fail'] = ($perFolder[$dir]['fail'] ?? 0) + 1;
    }

    if (($i + 1) % 25 === 0 || $i + 1 === count($wanted)) {
        printf("  %s of %s   %s downloaded, %s could not be found   %.0f MB\n",
            number_format($i + 1), number_format(count($wanted)),
            number_format($done), number_format(count($failed)), $bytes / 1048576);
    }
}

echo "\n";
printf("%-34s %8s %10s\n", 'FOLDER', 'GOT', 'NOT FOUND');
echo str_repeat('-', 56) . "\n";
ksort($perFolder);
foreach ($perFolder as $folder => $counts) {
    printf("%-34s %8s %10s\n", $folder . '/', number_format($counts['ok'] ?? 0), number_format($counts['fail'] ?? 0));
}
echo str_repeat('-', 56) . "\n";
printf("%s files, %.0f MB\n", number_format($done), $bytes / 1048576);

if ($failed) {
    $log = 'docs/uploads-not-found.txt';
    file_put_contents($log, implode("\n", $failed) . "\n");
    printf("\n%s were not on the server either — the database points at files that no\n", number_format(count($failed)));
    printf("longer exist. The list is in %s. Those show the page's own fallback.\n", $log);
}
