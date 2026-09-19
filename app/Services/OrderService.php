<?php

namespace App\Services;

use App\Models\Shop\Address;
use App\Models\Shop\Cart;
use App\Models\Shop\Coupon;
use App\Models\Shop\CouponUsage;
use App\Models\Shop\Order;
use App\Models\Shop\OrderItem;
use App\Models\Shop\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Turns a cart into an order.
 *
 * Totals are recomputed from the cart at the moment of placing — the posted
 * form is only trusted for the address, delivery and payment CHOICE, never for
 * money. Product and address details are snapshotted onto the order so it
 * still reads correctly if the catalogue or address book changes later.
 */
class OrderService
{
    public function __construct(private CartService $cart)
    {
    }

    /**
     * @throws \RuntimeException when the cart is empty or the address is not the buyer's
     */
    public function placeOrder(int $addressId, string $deliveryOption, string $paymentMode): Order
    {
        $userId = (int) Auth::id();
        $totals = $this->cart->totals($deliveryOption);

        if (empty($totals['lines'])) {
            throw new \RuntimeException('Your cart is empty.');
        }

        foreach ($totals['lines'] as $line) {
            if (! $this->cart->sellableVariant((int) $line['product_model_id'])) {
                throw new \RuntimeException('"' . $line['name'] . '" is no longer available. Please remove it from your cart.');
            }
        }

        $address = Address::where('id', $addressId)
            ->where('user_id', $userId)
            ->where('status_id', 1)
            ->first();

        if (! $address) {
            throw new \RuntimeException('Please choose a delivery address.');
        }

        return DB::transaction(function () use ($userId, $totals, $address, $deliveryOption, $paymentMode) {
            // The order number is read under a lock (see nextOrderNumber); deadlocks are retried up to 3 times.
            $order = new Order();
            $order->order_number = $this->nextOrderNumber();
            $order->user_id = $userId;

            $order->subtotal = $totals['subtotal'];
            $order->discount_amount = $totals['discount'];
            $order->coupon_code = $totals['coupon_code'];
            $order->shipping_amount = $totals['shipping'];
            $order->tax_amount = 0;                     // Prices are already tax-inclusive.
            $order->total_amount = $totals['total'];
            $order->currency = 'INR';
            $order->delivery_option = $deliveryOption;

            // Address snapshot
            $order->shipping_name = $address->name;
            $order->shipping_mobile = $address->mobile;
            $order->shipping_address_line_1 = $address->address_line_1;
            $order->shipping_address_line_2 = $address->address_line_2;
            $order->shipping_city = $address->city;
            $order->shipping_district = $address->district;
            $order->shipping_state = $address->state;
            $order->shipping_pincode = $address->pincode;

            $order->payment_mode = $paymentMode;
            // COD is settled on delivery; everything else waits for the gateway.
            $order->payment_status = $paymentMode === 'cod' ? 'cod_pending' : 'pending';
            $order->order_status = 'placed';
            $order->status_id = 1;
            $order->ip_address = request()->ip();
            $order->save();

            foreach ($totals['lines'] as $line) {
                $item = new OrderItem();
                $item->order_id = $order->id;
                $item->product_model_id = $line['product_model_id'];
                $item->product_name = $line['name'];
                $item->product_image = $line['image'];
                $item->brand_name = $line['brand'];
                $item->qty = $line['qty'];
                $item->unit_price = $line['price'];
                $item->unit_mrp = $line['mrp'];
                $item->line_total = $line['line_total'];
                $item->status_id = 1;
                $item->save();
            }

            $this->recordStatus($order, 'placed', 'Order placed by customer.');

            if ($totals['coupon_code'] && $totals['discount'] > 0) {
                $coupon = Coupon::where('code', $totals['coupon_code'])->first();

                if ($coupon) {
                    CouponUsage::create([
                        'coupon_id' => $coupon->id,
                        'user_id' => $userId,
                        'order_id' => $order->id,
                        'discount_amount' => $totals['discount'],
                        'ip_address' => request()->ip(),
                    ]);
                }
            }

            // Close the cart so a refresh cannot place the same order twice.
            $cart = Cart::where('id', $this->cart->current()->id)->first();
            if ($cart) {
                $cart->cart_status = 'converted';
                $cart->coupon_code = null;
                $cart->save();
            }

            return $order;
        }, 3);
    }

    /**
     * May this order move to $status? Forward along FLOW only; cancel only
     * before it ships; delivered and cancelled are final.
     */
    public function canMoveTo(Order $order, string $status): bool
    {
        $current = $order->order_status;

        if (in_array($current, ['delivered', 'cancelled'], true)) {
            return false;
        }

        if ($status === 'cancelled') {
            return in_array($current, ['placed', 'confirmed', 'packed'], true);
        }

        $from = array_search($current, Order::FLOW, true);
        $to = array_search($status, Order::FLOW, true);

        return $from !== false && $to !== false && $to > $from;
    }

    /** Cancelling gives the customer their coupon back. */
    public function releaseCoupon(Order $order): void
    {
        CouponUsage::where('order_id', $order->id)->delete();
    }

    public function recordStatus(Order $order, string $status, ?string $note = null): void
    {
        $order->order_status = $status;
        $order->save();

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'order_status' => $status,
            'note' => $note,
            'created_by' => Auth::id(),
            'ip_address' => request()->ip(),
        ]);
    }

    /** ABZ + yyyymmdd + zero-padded daily sequence, e.g. ABZ20260910C001 */
    private function nextOrderNumber(): string
    {
        $prefix = 'ABZ' . now()->format('Ymd') . 'C';

        $last = Order::where('order_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderByDesc('order_number')
            ->value('order_number');

        $next = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
