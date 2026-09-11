<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Services\EmergencyService;

class EmergencyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:auto_emergency_list'])->only(['index']);
        $this->middleware(['permission:auto_emergency_update'])->only(['update_stauts']);
    }

    public function index()
    {
        $emergency = EmergencyService::with('user')->get();
        return view('admin.emergency.index',compact('emergency'));
    }

    public function status_update(Request $request)
    {
        $emergency = EmergencyService::findOrFail($request->id);
        $emergency->status = $request->status;
        $emergency->save();
        return response()->json(array('success' => true, 'message' => 'Emergency Status Updated Successfully'));
    }
}
