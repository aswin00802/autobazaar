<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\DriverRequest;
use App\Http\Controllers\Controller;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:auto_driver_list'])->only(['index']);
        $this->middleware(['permission:auto_driver_approved'])->only(['approveDriverRequest']);
        $this->middleware(['permission:auto_driver_delete'])->only(['destroy']);
    }

    public function index()
    {
        $driverRequests = DriverRequest::with('autoArea')->latest()->get();

        return view('admin.driver.index',compact('driverRequests'));
    }

    public function approveDriverRequest($id)
    {
        $request = DriverRequest::find($id);

        if (!$request) {
            return redirect()->back()->with('error', 'Driver request not found.');
        }

        $request->status = 1;
        $request->save();

        return redirect()->back()->with('success', 'Driver request approved successfully.');
    }

    public function destroy($id)
    {
        $driverRequest = DriverRequest::find($id);

        if (!$driverRequest) {
            return redirect()->back()->with('error', 'Driver request not found.');
        }

        $driverRequest->status = 2;
        $driverRequest->save();

        return redirect()->back()->with('success', 'Driver request deleted successfully.');
    }
}
