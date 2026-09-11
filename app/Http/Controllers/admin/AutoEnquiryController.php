<?php

namespace App\Http\Controllers\admin;

use App\Models\Enquiry;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AutoEnquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:auto_enquiry_list'])->only(['index']);
        $this->middleware(['permission:auto_enquiry_update'])->only(['update_stauts']);
    }
    public function index()
    {
        $used_auto_enquirys = Enquiry::with(['user','user.autoAreas','auto', 'auto.autoBrands'])
        ->whereHas('auto', function($query){
            $query->whereIn('auto_usage_status', ['used_auto', 'private_cargo', 'bajaj']);
        })->orderBy('id', 'Desc')->get();

        $new_auto_enquirys = Enquiry::with(['user','user.autoAreas','auto', 'auto.autoBrands'])
        ->whereHas('auto', function($query){
            $query->where('auto_usage_status', 'new_auto');
        })->orderBy('id', 'Desc')->get();

        return view('admin.auto_enquiry.index',compact('used_auto_enquirys','new_auto_enquirys'));
    }

    public function update_stauts(Request $request)
    {
        $enquiryStatus = Enquiry::findOrFail($request->id);
        $enquiryStatus->interested_status = $request->status;
        $enquiryStatus->save();
        return response()->json(array('success' => true, 'message' => 'Enquiry Status Updated Successfully'));
    }
}
