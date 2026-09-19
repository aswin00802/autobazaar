<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Auto\Auto;
use App\Models\POSQuotation;
use Illuminate\Http\Request;

class POSQuotationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:pos_quotation'])->only(['index', 'create', 'getAutoModel']);
        // $this->middleware(['permission:add_pos_quotation'])->only(['create']);
    }

    public function index()
    {
        $quotations = POSQuotation::with('auto')->where('status_id',1)->latest()->get();
        return view('admin.quotatioin.pos.index',compact('quotations'));
    }

    public function create()
    {
        $brands = Auto::with('autoBrands')
                    ->where([
                        ['auto_usage_status', 'new_auto'],
                        ['auto_status', 'active'],
                        ['status', 1]
                    ])
                    ->select('auto_brand_id')
                    ->groupBy('auto_brand_id')
                    ->get();
        return view('admin.quotatioin.pos.create',compact('brands'));
    }

    public function getAutoModel(Request $request)
    {

    }
}
