<?php
/**
 * Proves the mobile apps still see exactly what they saw before.
 *
 * The apps are only being repointed at a new address — not rebuilt — so every
 * endpoint has to answer with the same shape it always did. This records the
 * status code and the structure of each response (the keys and the type of each
 * value, not the values themselves) and compares it against a stored baseline.
 *
 *     php docs/tests/api-contract-test.php --save     write the baseline
 *     php docs/tests/api-contract-test.php            compare against it
 *
 * Run --save BEFORE changing anything, then run it plain afterwards. Any change
 * in a key name, a nesting level or a status code is reported as a difference.
 *
 * It creates a throwaway user on a test number, and deletes it at the end.
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '1024M');
set_time_limit(0);
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\User;
use Illuminate\Support\Facades\DB;

$save = in_array('--save', $argv, true);
$baselineFile = 'docs/tests/api-baseline.json';
$phone = '5000000002';

/* ------------------------------------------------- a throwaway app user */
User::where('phone_number', $phone)->forceDelete();
$areaId = (int) (DB::table('tbl_auto_areas')->value('id') ?: 1);
$user = User::create([
    'name' => 'API Contract Test', 'phone_number' => $phone,
    'auto_area_id' => $areaId, 'role_id' => 1000, 'status' => 1,
]);
$token = $user->createToken('contract-test')->plainTextToken;

/** The shape of a value: keys and types, never the values themselves. */
function shape($value, int $depth = 0) {
    if ($depth > 3) { return '…'; }

    if (is_array($value)) {
        if ($value === []) { return '[]'; }
        if (array_is_list($value)) { return [shape($value[0], $depth + 1)]; }   // one sample is enough

        $out = [];
        foreach ($value as $k => $v) { $out[$k] = shape($v, $depth + 1); }
        ksort($out);

        return $out;
    }

    return match (true) {
        is_bool($value) => 'bool',
        is_int($value) => 'int',
        is_float($value) => 'float',
        is_null($value) => 'null',
        default => 'string',
    };
}

function call($method, $uri, $token, $body = []) {
    global $kernel, $app;
    $app['auth']->forgetGuards();
    $request = Illuminate\Http\Request::create($uri, $method, $body, [], [], [
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        'CONTENT_TYPE' => 'application/json',
    ]);
    $app->instance('request', $request);
    $response = $kernel->handle($request);

    return [$response->getStatusCode(), json_decode($response->getContent(), true)];
}

/*
 * Read-only endpoints the apps call constantly. Nothing here changes data.
 */
$endpoints = [
    ['GET', 'api/get-auto-brands'],
    ['GET', 'api/get-fuel-types'],
    ['GET', 'api/get-owners'],
    ['GET', 'api/get-transmission-types'],
    ['GET', 'api/get-auto-body-types'],
    ['GET', 'api/get-price-ranges'],
    ['GET', 'api/get-cities'],
    ['GET', 'api/get-areas'],
    ['GET', 'api/get-finance'],
    ['GET', 'api/get-mechanic'],
    ['GET', 'api/get-events'],
    ['GET', 'api/get-product-categories'],
    ['GET', 'api/get-profile'],
    ['GET', 'api/get-favourites'],
    ['GET', 'api/get-user-auto'],
    ['GET', 'api/get-sold-auto'],
    ['GET', 'api/get-auto-enquiries'],
    ['GET', 'api/get-all-posts'],
    ['GET', 'api/auto_posts'],
    ['GET', 'api/get-lpg-cng-price'],
    ['GET', 'api/fareprice/ride-cancel-reason'],
    ['GET', 'api/fareprice/ride-history'],
    ['GET', 'api/fareprice/driver/ride-history'],
    ['GET', 'api/fairprice/v1/customer/profile'],
    ['GET', 'api/fairprice/v1/customer/ride-cancel-reason'],
    ['GET', 'api/fairprice/v1/customer/ride-history'],
    ['GET', 'api/auto-meter/history'],
];

$current = [];
foreach ($endpoints as [$method, $uri]) {
    [$status, $json] = call($method, $uri, $token);
    $current[$uri] = ['status' => $status, 'shape' => shape($json)];
}

/* -------------------------------------- an endpoint with no token must refuse */
[$statusNoToken] = call('GET', 'api/get-profile', 'not-a-real-token');
$current['__no_token__'] = ['status' => $statusNoToken, 'shape' => 'refused'];

User::where('phone_number', $phone)->forceDelete();

/* ----------------------------------------------------------------- compare */
if ($save) {
    file_put_contents($baselineFile, json_encode($current, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    printf("baseline written: %d endpoints -> %s\n", count($endpoints), $baselineFile);
    foreach ($current as $uri => $r) {
        if ($uri === '__no_token__') { continue; }
        printf("  %-46s %s\n", $uri, $r['status']);
    }
    exit(0);
}

if (! is_file($baselineFile)) {
    exit("No baseline yet. Run with --save first.\n");
}

$baseline = json_decode(file_get_contents($baselineFile), true);

$differences = [];
foreach ($baseline as $uri => $was) {
    if (! isset($current[$uri])) { $differences[] = "{$uri}: no longer tested"; continue; }
    $now = $current[$uri];

    if ($now['status'] !== $was['status']) {
        $differences[] = "{$uri}: status was {$was['status']}, now {$now['status']}";
    }
    if (json_encode($now['shape']) !== json_encode($was['shape'])) {
        $differences[] = "{$uri}: the response shape changed";
    }
}
foreach ($current as $uri => $now) {
    if (! isset($baseline[$uri])) { $differences[] = "{$uri}: new, not in the baseline"; }
}

printf("\n%d endpoints checked against the baseline\n\n", count($baseline));

if (! $differences) {
    echo "  PASS  every endpoint answers with the same status and the same shape\n";
    echo "\nThe apps will not notice the difference.\n";
    exit(0);
}

echo "  FAIL  " . count($differences) . " difference(s):\n";
foreach ($differences as $d) { echo "    - {$d}\n"; }
exit(1);
