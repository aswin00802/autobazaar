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
    public function index()
    {
        $quotations = Quotation::with(['user','user.autoAreas','auto', 'auto.autoBrands'])->latest()->get();
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
