<?php

namespace App\Http\Controllers\admin\service;

use Illuminate\Http\Request;
use App\Models\Services\ReFinance;
use App\Http\Controllers\Controller;

class ReFinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:re_finance'])->only('index');
        $this->middleware(['permission:re_finance_update'])->only(['update_stauts']);
    }

    public function index()
    {
        $refinances = ReFinance::all();
        return view('admin.service.re_finance.index', compact('refinances'));
    }

    public function update_stauts(Request $request)
    {
        $refinanceStatus = ReFinance::findOrFail($request->id);
        $refinanceStatus->status = $request->status;
        $refinanceStatus->save();
        return response()->json(array('success' => true, 'message' => 'Re Finance Status Updated Successfully'));
    }
}
