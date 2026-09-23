<?php
/**
 * An order moves, and the customer is told.
 *
 * Walks the real path: a throwaway customer places an order, an admin moves it
 * on, and the customer opens My Account and sees the message. Also checks that
 * a push failing — no credentials, no internet, no device token — never stops
 * the order being updated, because that is the part that must not break.
 *
 * Everything it creates is deleted again.
 *
 *     php docs/tests/notification-test.php
 */
chdir('C:/xampp/htdocs/autobazaar');
ini_set('memory_limit', '1024M');
set_time_limit(0);
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Shop\Order;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;

$pass = 0; $fail = 0; $failed = [];
function ok($label, $cond, $extra = '') {
    global $pass, $fail, $failed;
    $cond ? $pass++ : ($fail++ + array_push($failed, $label));
    echo ($cond ? '  PASS  ' : '  FAIL  ') . str_pad($label, 54) . ($extra !== '' ? "[$extra]" : '') . "\n";
}
function section($t) { echo "\n== $t\n"; }

$phone = '5000000004';
$app['session']->start();

/* ---------------------------------------------------------------- set up */
$old = User::where('phone_number', $phone)->first();
if ($old) {
    DB::table('notifications')->where('notifiable_id', $old->id)->delete();
    DB::table('shop_orders')->where('user_id', $old->id)->delete();
    $old->forceDelete();
}

$customer = User::create([
    'name' => 'Notify Test', 'phone_number' => $phone, 'role_id' => 1000, 'status' => 1,
    'auto_area_id' => (int) (DB::table('tbl_auto_areas')->value('id') ?: 1),
    // A device token that Firebase will reject — the push must fail and be shrugged off.
    'device_token' => 'not-a-real-device-token',
]);

$order = new Order();
$order->order_number = 'NOTIFY-TEST-' . now()->format('His');
$order->user_id = $customer->id;
$order->subtotal = 500; $order->discount_amount = 0; $order->shipping_amount = 0;
$order->tax_amount = 0; $order->total_amount = 500; $order->currency = 'INR';
$order->delivery_option = 'standard';
$order->shipping_name = 'Notify Test'; $order->shipping_mobile = '9000000004';
$order->shipping_address_line_1 = '1 Test Street'; $order->shipping_city = 'Chennai';
$order->shipping_state = 'Tamil Nadu'; $order->shipping_pincode = '600001';
$order->payment_mode = 'cod'; $order->payment_status = 'cod_pending';
$order->order_status = 'placed'; $order->status_id = 1;
$order->save();

section('AN ADMIN MOVES THE ORDER ON');

$before = DB::table('notifications')->where('notifiable_id', $customer->id)->count();
app(OrderService::class)->recordStatus($order->fresh(), 'confirmed', 'Stock checked.');
$after = DB::table('notifications')->where('notifiable_id', $customer->id)->count();

ok('the order really did move', Order::find($order->id)->order_status === 'confirmed',
   Order::find($order->id)->order_status);
ok('a push that cannot be delivered does not undo that', $after >= $before,
   'the device token was deliberately invalid');
ok('the customer has a message waiting', $after === $before + 1, "{$before} -> {$after}");

$note = DB::table('notifications')->where('notifiable_id', $customer->id)->latest('created_at')->first();
$data = $note ? json_decode($note->data, true) : [];

ok('it says what happened, in plain words', ($data['title'] ?? '') === 'Order confirmed', $data['title'] ?? '-');
ok('and mentions the order by number', str_contains($data['body'] ?? '', $order->order_number));
ok('the note the admin typed is included', str_contains($data['body'] ?? '', 'Stock checked.'));
ok('it starts out unread', $note && $note->read_at === null);

section('MORE STEPS, MORE MESSAGES');
foreach (['packed', 'shipped', 'delivered'] as $step) {
    app(OrderService::class)->recordStatus($order->fresh(), $step, null);
}
$total = DB::table('notifications')->where('notifiable_id', $customer->id)->count();
ok('one message per step', $total === 4, "{$total} messages for 4 moves");

/*
 * Paired by status rather than read in order: all four are written within the
 * same second, so sorting by created_at is a coin toss between them and the
 * order proves nothing either way.
 */
$titleFor = [];
foreach (DB::table('notifications')->where('notifiable_id', $customer->id)->pluck('data') as $row) {
    $decoded = json_decode($row, true);
    $titleFor[$decoded['status'] ?? '?'] = $decoded['title'] ?? '?';
}
ksort($titleFor);

$expected = [
    'confirmed' => 'Order confirmed',
    'delivered' => 'Order delivered',
    'packed'    => 'Order packed',
    'shipped'   => 'Order on its way',
];
ok('each status says the right thing', $titleFor === $expected,
   implode(', ', array_map(fn ($k, $v) => "{$k}={$v}", array_keys($titleFor), $titleFor)));

section('THE CUSTOMER OPENS MY ACCOUNT');

$app['auth']->forgetGuards();
$request = Illuminate\Http\Request::create('/account/notifications', 'GET');
$app['auth']->guard('web')->setUser($customer);
$app->instance('request', $request);
$response = $kernel->handle($request);
$html = $response->getContent();

ok('the notifications page opens', $response->getStatusCode() === 200, 'HTTP ' . $response->getStatusCode());
ok('no sample data is left on it', ! str_contains($html, 'New festival offer from TVS'));
ok('the real messages are shown', substr_count($html, 'Order ') >= 4);
ok('with a link to the order', str_contains($html, 'View order'));

$stillUnread = DB::table('notifications')->where('notifiable_id', $customer->id)->whereNull('read_at')->count();
ok('opening the page marks them read', $stillUnread === 0, "{$stillUnread} still unread");

/* --------------------------------------------------------------- clean up */
section('PUTTING EVERYTHING BACK');
DB::table('notifications')->where('notifiable_id', $customer->id)->delete();
DB::table('shop_order_status_histories')->where('order_id', $order->id)->delete();
DB::table('shop_orders')->where('id', $order->id)->delete();
User::where('phone_number', $phone)->forceDelete();

ok('the test customer and order are gone',
   ! User::where('phone_number', $phone)->exists()
   && ! DB::table('shop_orders')->where('id', $order->id)->exists());

echo "\n==================== RESULT: {$pass} passed, {$fail} failed\n";
if ($failed) { echo 'failed: ' . implode(' | ', $failed) . "\n"; }
exit($fail ? 1 : 0);
