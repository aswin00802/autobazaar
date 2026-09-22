<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Auto\Quotation;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:quotation'])->only(['index']);
        $this->middleware(['permission:quotation_status_update'])->only(['update_stauts']);
    }
    public function index(Request $request)
    {
        $query = Quotation::with(['user','user.autoAreas','auto', 'auto.autoBrands', 'auto.autoFueltype'])->latest();

        // Searching runs over every quotation, not just the page on screen.
        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                                                  ->orWhere('phone_number', 'like', "%{$search}%"))
                  ->orWhereHas('auto', fn ($a) => $a->where('auto_unique_id', 'like', "%{$search}%")
                                                     ->orWhere('specific_model', 'like', "%{$search}%")
                                                     ->orWhereHas('autoBrands', fn ($b) => $b->where('brand_name', 'like', "%{$search}%")));
            });
        }

        // Shows every quotation in one page, with the table's own search and
        // export, like the rest of the admin lists (config/admin_lists.php).
        $perPage = (int) config('admin_lists.per_page.quotations', 0);
        $quotations = $perPage > 0 ? $query->paginate($perPage)->withQueryString() : $query->get();

        return view('admin.quotatioin.index',compact('quotations'));
    }

    public function update_stauts(Request $request)
    {
        $enquiryStatus = Quotation::findOrFail($request->id);
        $enquiryStatus->status_id = $request->status;
        $enquiryStatus->save();
        return response()->json(array('success' => true, 'message' => 'Quotation Status Updated Successfully'));
    }
}
