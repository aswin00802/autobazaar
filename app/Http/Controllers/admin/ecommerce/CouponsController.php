<?php

namespace App\Http\Controllers\admin\ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Shop\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/** Discount codes used at accessory checkout. */
class CouponsController extends Controller
{
    public function index()
    {
        return view('admin.ecommerce.coupons.index', [
            'coupons' => Coupon::withCount('usages')->latest('id')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.ecommerce.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());

        if (Coupon::where('code', strtoupper($request->code))->exists()) {
            return redirect()->route('ecommerce.coupons.create')->with('error', 'This coupon code already exists');
        }

        $coupon = new Coupon();
        $this->fill($coupon, $request);
        $coupon->created_by = Auth::user()->id;
        $coupon->ip_address = $request->ip();
        $coupon->save();

        return redirect()->route('ecommerce.coupons')->with('success', 'Coupon created successfully');
    }

    public function edit($id)
    {
        return view('admin.ecommerce.coupons.edit', ['coupon' => Coupon::findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $request->validate($this->rules());

        $coupon = Coupon::findOrFail($id);

        if (Coupon::where('code', strtoupper($request->code))->where('id', '!=', $id)->exists()) {
            return redirect()->back()->with('error', 'This coupon code already exists');
        }

        $this->fill($coupon, $request);
        $coupon->ip_address = $request->ip();
        $coupon->save();

        return redirect()->route('ecommerce.coupons')->with('success', 'Coupon updated successfully');
    }

    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer']);

        $coupon = Coupon::findOrFail($request->id);
        $coupon->status_id = 0;
        $coupon->save();

        return redirect()->route('ecommerce.coupons')->with('success', 'Coupon removed successfully');
    }

    private function rules(): array
    {
        return [
            'code'                => 'required|string|max:50',
            'label'               => 'nullable|string|max:255',
            'discount_type'       => 'required|in:percent,flat',
            'discount_value'      => 'required|numeric|min:0',
            'min_order_amount'    => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit'         => 'nullable|integer|min:1',
            'per_user_limit'      => 'nullable|integer|min:1',
            'valid_from'          => 'nullable|date',
            'valid_to'            => 'nullable|date|after_or_equal:valid_from',
        ];
    }

    private function fill(Coupon $coupon, Request $request): void
    {
        $coupon->code                = strtoupper(trim($request->code));
        $coupon->label               = $request->label;
        $coupon->discount_type       = $request->discount_type;
        $coupon->discount_value      = $request->discount_value;
        $coupon->min_order_amount    = $request->min_order_amount ?? 0;
        $coupon->max_discount_amount = $request->max_discount_amount;
        $coupon->usage_limit         = $request->usage_limit;
        $coupon->per_user_limit      = $request->per_user_limit ?? 1;
        $coupon->first_order_only    = $request->has('first_order_only') ? 1 : 0;
        $coupon->valid_from          = $request->valid_from;
        $coupon->valid_to            = $request->valid_to;
        $coupon->status_id           = $request->has('status_id') ? 1 : 0;
    }
}
