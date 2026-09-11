<?php

namespace App\Http\Controllers\admin\service;

use Illuminate\Http\Request;
use App\Models\Services\RtoService;
use App\Http\Controllers\Controller;

class RTOController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:rto'])->only('index');
        $this->middleware(['permission:rto_update'])->only(['update_stauts']);
    }

    public function index()
    {
        $rtos = RtoService::all();
        return view('admin.service.rto.index', compact('rtos'));
    }

    public function update_stauts(Request $request)
    {
        $refinanceStatus = RtoService::findOrFail($request->id);
        $refinanceStatus->status = $request->status;
        $refinanceStatus->save();
        return response()->json(array('success' => true, 'message' => 'RTO Status Updated Successfully'));
    }
}
