<?php

namespace App\Http\Controllers\Web\Shop;

use App\Http\Controllers\Controller;
use App\Models\Shop\Address;
use App\Models\Shop\Order;
use App\Services\CartService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Checkout and orders. Everything here requires a signed-in customer —
 * browsing and the cart itself stay public.
 */
class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cart,
        private OrderService $orders,
    ) {
    }

    public function index(Request $request)
    {
        $delivery = $request->input('delivery_option', 'standard');

        return view('site.checkout', [
            ...$this->shared(),
            'totals'         => $this->cart->totals($delivery),
            'addresses'      => Address::where('user_id', Auth::id())
                                    ->where('status_id', 1)
                                    ->orderByDesc('is_default')->orderBy('id')->get(),
            'deliveryOptions' => CartService::DELIVERY,
            'paymentMethods' => $this->paymentMethods(),
            'freeShippingAbove' => CartService::FREE_SHIPPING_ABOVE,
        ]);
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'label'          => 'required|string|max:50',
            'name'           => 'required|string|max:255',
            'mobile'         => 'required|string|max:20',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'district'       => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'pincode'        => 'nullable|string|max:10',
        ]);

        $isFirst = ! Address::where('user_id', Auth::id())->where('status_id', 1)->exists();

        $address                 = new Address();
        $address->user_id        = Auth::id();
        $address->label          = $request->label;
        $address->name           = $request->name;
        $address->mobile         = $request->mobile;
        $address->address_line_1 = $request->address_line_1;
        $address->address_line_2 = $request->address_line_2;
        $address->city           = $request->city;
        $address->district       = $request->district;
        $address->state          = $request->state;
        $address->pincode        = $request->pincode;
        $address->is_default     = $isFirst ? 1 : 0;
        $address->status_id      = 1;
        $address->ip_address     = $request->ip();
        $address->save();

        return redirect()->route('site.checkout')->with('success', 'Address saved.');
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'address_id'      => 'required|integer',
            'delivery_option' => 'required|in:standard,express',
            'payment_mode'    => 'required|in:' . implode(',', $this->enabledPaymentModes()),
        ]);

        try {
            $order = $this->orders->placeOrder(
                (int) $request->address_id,
                $request->delivery_option,
                $request->payment_mode,
            );
        } catch (\RuntimeException $e) {
            return redirect()->route('site.checkout')->with('error', $e->getMessage());
        }

        // Online payment would hand off to Razorpay here — see
        // App\Services\RazorpayPaymentService, already used for ride payments.
        return redirect()->route('site.order.confirmed', $order->order_number);
    }

    public function confirmed(string $orderNumber)
    {
        $order = $this->findOrder($orderNumber);

        return view('site.order-confirmed', [
            ...$this->shared(),
            'order' => $order,
        ]);
    }

    /** A customer may only ever open their own order. */
    private function findOrder(string $orderNumber): Order
    {
        return Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items', 'history'])
            ->firstOrFail();
    }

    /**
     * Online payment is switched on here once the gateway is connected.
     * Until then only Cash on Delivery can be chosen — nothing may look paid.
     */
    private const ONLINE_PAYMENTS_ENABLED = false;

    private function enabledPaymentModes(): array
    {
        return self::ONLINE_PAYMENTS_ENABLED ? ['upi', 'card', 'netbanking', 'wallet', 'cod'] : ['cod'];
    }

    private function paymentMethods(): array
    {
        return array_map(
            fn (array $m) => $m + ['enabled' => in_array($m['id'], $this->enabledPaymentModes(), true)],
            $this->allPaymentMethods(),
        );
    }

    private function allPaymentMethods(): array
    {
        return [
            ['id' => 'upi',        'label' => 'UPI',                    'note' => '(Google Pay, PhonePe, Paytm, etc.)', 'brands' => ['GPay', 'PhonePe', 'Paytm']],
            ['id' => 'card',       'label' => 'Credit / Debit Card',    'note' => null,                                  'brands' => ['VISA', 'Mastercard', 'RuPay']],
            ['id' => 'netbanking', 'label' => 'Net Banking',            'note' => null,                                  'brands' => ['Bank']],
            ['id' => 'wallet',     'label' => 'Wallets',                'note' => '(Amazon Pay, etc.)',                  'brands' => ['Amazon Pay']],
            ['id' => 'cod',        'label' => 'Cash on Delivery (COD)', 'note' => 'Available for select locations',      'brands' => ['COD']],
        ];
    }

    /** Chrome the prototype layout needs until cutover. */
    private function shared(): array
    {
        return [
            'site'      => require resource_path('fixtures/site.php'),
            'locations' => require resource_path('fixtures/locations.php'),
        ];
    }
}
