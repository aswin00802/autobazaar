<?php

namespace App\Services;

use App\Models\Shop\Cart;
use App\Models\Shop\CartItem;
use App\Models\Shop\Coupon;
use App\Models\Shop\CouponUsage;
use App\Models\Shop\Order;
use App\Models\spareparts\ProductBrandModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Accessories cart and pricing.
 *
 * Guests build a cart against their session id; on login it is merged into the
 * user's cart so nothing is lost at sign-in. All money is recalculated here
 * from stored line prices — never trusted from the request.
 */
class CartService
{
    /** Free standard shipping at or above this order value. */
    public const FREE_SHIPPING_ABOVE = 999;

    public const DELIVERY = [
        'standard' => ['label' => 'Standard Delivery', 'note' => '3 - 6 business days', 'price' => 0,  'free_above' => true],
        'express'  => ['label' => 'Express Delivery',  'note' => '1 - 3 business days', 'price' => 69, 'free_above' => false],
    ];

    /* ==================================================================== cart */

    /**
     * The caller's active cart. Creates one on first use.
     */
    public function current(bool $createIfMissing = true): ?Cart
    {
        $query = Cart::where('cart_status', 'active')->where('status_id', 1);

        if (Auth::check()) {
            $cart = (clone $query)->where('user_id', Auth::id())->latest('id')->first();
        } else {
            $cart = (clone $query)->whereNull('user_id')
                ->where('session_id', session()->getId())
                ->latest('id')->first();
        }

        if (! $cart && $createIfMissing) {
            $cart = new Cart();
            $cart->user_id = Auth::id();
            $cart->session_id = Auth::check() ? null : session()->getId();
            $cart->cart_status = 'active';
            $cart->status_id = 1;
            $cart->ip_address = request()->ip();
            $cart->save();
        }

        return $cart;
    }

    /**
     * Fold a guest cart into the signed-in user's cart. Call on login.
     */
    public function mergeGuestCart(int $userId, string $sessionId): void
    {
        $guest = Cart::where('cart_status', 'active')
            ->whereNull('user_id')
            ->where('session_id', $sessionId)
            ->with('items')
            ->latest('id')
            ->first();

        if (! $guest || $guest->items->isEmpty()) {
            return;
        }

        $target = Cart::where('cart_status', 'active')->where('user_id', $userId)->latest('id')->first();

        if (! $target) {
            // Nothing to merge into — just claim the guest cart.
            $guest->user_id = $userId;
            $guest->session_id = null;
            $guest->save();

            return;
        }

        DB::transaction(function () use ($guest, $target) {
            foreach ($guest->items as $item) {
                $existing = CartItem::where('cart_id', $target->id)
                    ->where('product_model_id', $item->product_model_id)
                    ->first();

                if ($existing) {
                    $existing->qty = min(10, $existing->qty + $item->qty);
                    $existing->save();
                } else {
                    $item->cart_id = $target->id;
                    $item->save();
                }
            }

            $guest->cart_status = 'merged';
            $guest->save();
        });
    }

    public function add(int $productModelId, int $qty = 1): CartItem
    {
        $variant = ProductBrandModel::with('product')->findOrFail($productModelId);

        $price = (float) ($variant->offer_price ?: $variant->price);
        $cart = $this->current();

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_model_id', $productModelId)
            ->first();

        if ($item) {
            $item->qty = min(10, $item->qty + $qty);
        } else {
            $item = new CartItem();
            $item->cart_id = $cart->id;
            $item->product_model_id = $productModelId;
            $item->qty = min(10, max(1, $qty));
            $item->ip_address = request()->ip();
        }

        // Refresh the captured price each time it is touched.
        $item->unit_price = $price;
        $item->unit_mrp = (float) $variant->price;
        $item->status_id = 1;
        $item->save();

        return $item;
    }

    public function updateQty(int $itemId, int $qty): void
    {
        $item = $this->itemFor($itemId);

        if (! $item) {
            return;
        }

        if ($qty < 1) {
            $item->delete();

            return;
        }

        $item->qty = min(10, $qty);
        $item->save();
    }

    public function remove(int $itemId): void
    {
        $this->itemFor($itemId)?->delete();
    }

    /** Guards against editing another visitor's cart by guessing an item id. */
    private function itemFor(int $itemId): ?CartItem
    {
        $cart = $this->current(false);

        return $cart
            ? CartItem::where('id', $itemId)->where('cart_id', $cart->id)->first()
            : null;
    }

    public function itemCount(): int
    {
        $cart = $this->current(false);

        return $cart ? (int) CartItem::where('cart_id', $cart->id)->sum('qty') : 0;
    }

    /* ================================================================= display */

    /**
     * Cart lines shaped for the views, with product details resolved.
     */
    public function lines(): array
    {
        $cart = $this->current(false);

        if (! $cart) {
            return [];
        }

        return CartItem::where('cart_id', $cart->id)
            ->with(['productModel.product.subCategory', 'productModel.autoBrands'])
            ->orderBy('id')
            ->get()
            ->map(function (CartItem $item) {
                $product = $item->productModel?->product;
                $image = $product?->image;

                return [
                    'id' => $item->id,
                    'product_model_id' => $item->product_model_id,
                    'name' => $product ? trim(str_replace('?', '–', $product->name)) : 'Item unavailable',
                    'subtitle' => $product?->subCategory->name ?? null,
                    'brand' => $item->productModel?->autoBrands->brand_name ?? null,
                    'price' => (float) $item->unit_price,
                    'mrp' => (float) $item->unit_mrp,
                    'qty' => (int) $item->qty,
                    'line_total' => $item->line_total,
                    'image' => $this->resolveImage($image),
                    'art' => $this->artFor($product?->subCategory->slug ?? null),
                ];
            })
            ->all();
    }

    /**
     * Stored path, but only if the file is really there.
     *
     * Directory listed once per request rather than a stat per line — and in
     * this environment every stored product image is missing anyway, so the
     * cart falls back to drawn placeholders. See PreviewController::resolveImage.
     */
    private array $dirCache = [];

    private function resolveImage(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $dir = dirname($path);

        $this->dirCache[$dir] ??= File::isDirectory(public_path($dir))
            ? array_flip(scandir(public_path($dir)) ?: [])
            : [];

        return isset($this->dirCache[$dir][basename($path)]) ? $path : null;
    }

    private function artFor(?string $subcategorySlug): string
    {
        return ['floor-mats' => 'mat', 'auto-cover' => 'cover', 'rain-cutters' => 'curtain'][$subcategorySlug] ?? 'grid';
    }

    /* ================================================================= totals */

    /**
     * Recalculated from stored line prices. Never trusts posted amounts.
     */
    public function totals(?string $deliveryOption = 'standard'): array
    {
        $lines = $this->lines();
        $subtotal = (float) collect($lines)->sum('line_total');

        $cart = $this->current(false);
        $coupon = $this->resolveCoupon($cart?->coupon_code, $subtotal);
        $discount = $coupon ? $coupon->discountOn($subtotal) : 0.0;

        $option = self::DELIVERY[$deliveryOption] ?? self::DELIVERY['standard'];
        $shipping = ($option['free_above'] && $subtotal >= self::FREE_SHIPPING_ABOVE)
            ? 0.0
            : (float) $option['price'];

        // Nothing to ship, nothing to charge for shipping.
        if ($subtotal <= 0) {
            $shipping = 0.0;
        }

        return [
            'lines' => $lines,
            'item_count' => (int) collect($lines)->sum('qty'),
            'subtotal' => $subtotal,
            'coupon_code' => $coupon?->code,
            'coupon_label' => $coupon?->label,
            'discount' => $discount,
            'shipping' => $shipping,
            'delivery_option' => $option === self::DELIVERY['express'] ? 'express' : $deliveryOption,
            'total' => max(0, $subtotal - $discount + $shipping),
        ];
    }

    /* ================================================================ coupons */

    /**
     * @return array{ok: bool, message: string}
     */
    public function applyCoupon(string $code): array
    {
        $code = strtoupper(trim($code));

        if ($code === '') {
            return ['ok' => false, 'message' => 'Enter a coupon code.'];
        }

        $subtotal = (float) collect($this->lines())->sum('line_total');
        $coupon = Coupon::where('code', $code)->where('status_id', 1)->first();

        if (! $coupon) {
            return ['ok' => false, 'message' => 'That coupon code is not valid.'];
        }

        if ($coupon->valid_from && $coupon->valid_from->isFuture()) {
            return ['ok' => false, 'message' => 'This coupon is not active yet.'];
        }

        if ($coupon->valid_to && $coupon->valid_to->isPast()) {
            return ['ok' => false, 'message' => 'This coupon has expired.'];
        }

        if ($subtotal < (float) $coupon->min_order_amount) {
            return [
                'ok' => false,
                'message' => 'Add items worth ₹' . number_format($coupon->min_order_amount) . ' to use this coupon.',
            ];
        }

        if (Auth::check() && ! $this->userMayUse($coupon, (int) Auth::id())) {
            return ['ok' => false, 'message' => 'You have already used this coupon.'];
        }

        $cart = $this->current();
        $cart->coupon_code = $coupon->code;
        $cart->save();

        return ['ok' => true, 'message' => $coupon->label ?: 'Coupon applied.'];
    }

    public function removeCoupon(): void
    {
        $cart = $this->current(false);

        if ($cart) {
            $cart->coupon_code = null;
            $cart->save();
        }
    }

    /** Re-checked at checkout, because a coupon can lapse while the cart sits. */
    private function resolveCoupon(?string $code, float $subtotal): ?Coupon
    {
        if (! $code) {
            return null;
        }

        $coupon = Coupon::where('code', $code)->where('status_id', 1)->first();

        if (! $coupon
            || ($coupon->valid_from && $coupon->valid_from->isFuture())
            || ($coupon->valid_to && $coupon->valid_to->isPast())
            || $subtotal < (float) $coupon->min_order_amount
            || (Auth::check() && ! $this->userMayUse($coupon, (int) Auth::id()))
        ) {
            return null;
        }

        return $coupon;
    }

    private function userMayUse(Coupon $coupon, int $userId): bool
    {
        $used = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->count();

        if ($coupon->per_user_limit !== null && $used >= (int) $coupon->per_user_limit) {
            return false;
        }

        if ($coupon->usage_limit !== null
            && CouponUsage::where('coupon_id', $coupon->id)->count() >= (int) $coupon->usage_limit) {
            return false;
        }

        if ($coupon->first_order_only && Order::where('user_id', $userId)->exists()) {
            return false;
        }

        return true;
    }
}
