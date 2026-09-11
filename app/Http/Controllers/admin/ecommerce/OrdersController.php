<?php

namespace App\Http\Controllers\admin\ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Shop\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

/**
 * Accessory orders placed through the website (`shop_orders`).
 *
 * The older screen under Auto Spare Parts reads `sparepart_orders`, which
 * holds one row per product with no price and no payment. Both are reachable
 * from the E-commerce menu; this one is the live checkout flow.
 */
class OrdersController extends Controller
{
    public function __construct(private OrderService $orders)
    {
    }

    public function index(Request $request, ?string $status = null)
    {
        $query = Order::with('items')->where('status_id', 1)->latest('id');

        if ($status && $status !== 'all') {
            $query->where('order_status', $status);
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($sub) use ($q) {
                $sub->where('order_number', 'like', "%{$q}%")
                    ->orWhere('shipping_name', 'like', "%{$q}%")
                    ->orWhere('shipping_mobile', 'like', "%{$q}%");
            });
        }

        return view('admin.ecommerce.orders.index', [
            'orders' => $query->paginate(20)->withQueryString(),
            'status' => $status ?? 'all',
            'counts' => $this->counts(),
            'flow' => Order::FLOW,
        ]);
    }

    public function show($id)
    {
        return view('admin.ecommerce.orders.view', [
            'order' => Order::with(['items', 'history', 'user'])->findOrFail($id),
            'flow' => Order::FLOW,
        ]);
    }

    public function statusUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'order_status' => 'required|in:' . implode(',', array_merge(Order::FLOW, ['cancelled'])),
            'note' => 'nullable|string|max:255',
        ]);

        $order = Order::findOrFail($request->id);

        if ($order->order_status === $request->order_status) {
            return redirect()->back()->with('error', 'Order is already marked ' . $request->order_status . '.');
        }

        $this->orders->recordStatus($order, $request->order_status, $request->note);

        return redirect()->back()->with('success', 'Order status updated successfully.');
    }

    public function paymentUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'payment_status' => 'required|in:pending,cod_pending,paid,failed,refunded',
        ]);

        $order = Order::findOrFail($request->id);
        $order->payment_status = $request->payment_status;

        if ($request->payment_status === 'paid' && ! $order->paid_at) {
            $order->paid_at = now();
        }

        $order->save();

        return redirect()->back()->with('success', 'Payment status updated successfully.');
    }

    /** Soft delete, matching how the auto modules "delete". */
    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $order = Order::findOrFail($request->id);
        $order->status_id = 0;
        $order->save();

        return redirect()->route('ecommerce.orders')->with('success', 'Order removed successfully.');
    }

    private function counts(): array
    {
        $counts = Order::where('status_id', 1)
            ->selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status')
            ->toArray();

        $counts['all'] = array_sum($counts);

        return $counts;
    }
}
