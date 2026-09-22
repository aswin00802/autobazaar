<?php

namespace App\Http\Controllers\Web\Shop;

use App\Http\Controllers\Controller;
use App\Services\CartService;
use Illuminate\Http\Request;

/**
 * Cart actions. Public on purpose — a visitor builds a cart as a guest and
 * only signs in at checkout, where the guest cart is merged into their account.
 */
class CartController extends Controller
{
    public function __construct(private CartService $cart)
    {
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_model_id' => 'required|integer|exists:product_brand_models,id',
            'qty'              => 'nullable|integer|min:1|max:10',
        ]);

        try {
            $this->cart->add((int) $request->product_model_id, (int) ($request->qty ?? 1));
        } catch (\RuntimeException $e) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $e->getMessage()], 422)
                : redirect()->back()->with('error', $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Added to your cart.',
                'count'   => $this->cart->itemCount(),
            ]);
        }

        return redirect()->back()->with('success', 'Added to your cart.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'qty'     => 'required|integer|min:0|max:10',
        ]);

        $this->cart->updateQty((int) $request->item_id, (int) $request->qty);

        return $this->respond($request);
    }

    public function remove(Request $request)
    {
        $request->validate(['item_id' => 'required|integer']);

        $this->cart->remove((int) $request->item_id);

        return $this->respond($request, 'Item removed from your cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['code' => 'required|string|max:50']);

        $result = $this->cart->applyCoupon($request->code);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => $result['ok'],
                'message' => $result['message'],
                'totals'  => $this->cart->totals($request->input('delivery_option', 'standard')),
            ]);
        }

        return redirect()->back()->with($result['ok'] ? 'success' : 'error', $result['message']);
    }

    public function removeCoupon(Request $request)
    {
        $this->cart->removeCoupon();

        return $this->respond($request, 'Coupon removed.');
    }

    /** Public cart page — guests can review and edit before they sign in. */
    public function show()
    {
        return view('site.cart', [
            'site' => \App\Support\SiteData::site(),
            'locations' => require resource_path('fixtures/locations.php'),
            'totals' => $this->cart->totals('standard'),
        ]);
    }

    /** Powers the header cart badge. */
    public function count()
    {
        return response()->json(['count' => $this->cart->itemCount()]);
    }

    private function respond(Request $request, ?string $message = null)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'count'   => $this->cart->itemCount(),
                'totals'  => $this->cart->totals($request->input('delivery_option', 'standard')),
            ]);
        }

        return $message
            ? redirect()->back()->with('success', $message)
            : redirect()->back();
    }
}
