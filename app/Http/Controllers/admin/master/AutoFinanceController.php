<?php

namespace App\Http\Controllers\admin\master;

use Illuminate\Http\Request;
use App\Models\Masters\Seller;
use App\Models\Services\Finance;
use App\Http\Controllers\Controller;
use App\Models\Masters\AutoFuelType;
use Illuminate\Support\Facades\Auth;

class AutoFinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:auto_finance'])->only('index');
        $this->middleware(['permission:add_auto_finance'])->only(['create','store']);
        $this->middleware(['permission:edit_auto_finance'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_auto_finance'])->only(['destroy']);
    }

    public function index()
    {
        $finances = Finance::where('status_id','!=',3)->latest()->get();
        return view('admin.masters.finance.index', compact('finances'));
    }

    public function create()
    {
        return view('admin.masters.finance.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'finance_name'  => 'required',
            'finance_type'  => 'required',
            'address'       => 'required',
            'location'      => 'required',
            'contact'       => 'required',
            'finance_image' => 'nullable|file',
        ]);
        
        if(Finance::where('contact',$request->contact)->exists()){
            return redirect()->route('masters.auto-finance.create')->with('error','This Auto Finance Already Exit');
        } else {
            
            $finance = new Finance();
            $finance->user_id           = Auth::user()->id;
            $finance->finance_name     = $request->finance_name;
            $finance->finance_type     = $request->finance_type;
            $finance->address          = $request->address;
            $finance->location         = $request->location;
            $finance->contact          = $request->contact;
            if ($request->hasFile('finance_image')) {
                $finance->image = uploadedAsset($request, 'finance_image', 'finance_image'.time(), 'finance');
            }
            $finance->save();
            return redirect()->route('masters.auto-finance')->with('success','Auto Finance Successfully Created.');
        }
        
    }

    public function edit($id)
    {
        $finance   = Finance::findOrFail($id);
        return view('admin.masters.finance.edit',compact('finance'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'finance_name'  => 'required',
            'finance_type'  => 'required',
            'address'       => 'required',
            'location'      => 'required',
            'contact'       => 'required',
            'finance_image' => 'nullable|file',
        ]);
        $finance                   = Finance::findOrFail($request->id);
        $finance->finance_name     = $request->finance_name;
        $finance->finance_type     = $request->finance_type;
        $finance->address          = $request->address;
        $finance->location         = $request->location;
        $finance->contact          = $request->contact;
        if ($request->hasFile('finance_image')) {
            if(isset($finance->image)){
                if(file_exists(public_path($finance->image))){
                    unlink(public_path($finance->image));
                }
            }
            $finance->image = uploadedAsset($request, 'finance_image', 'finance_image'.time(), 'finance');
        }
        $finance->save();
        return redirect()->route('masters.auto-finance')->with('success','Auto Finance Successfully Updated.');
    }

    public function destroy(Request $request)
    {
        $finance = Finance::find($request->id);
        if ($finance) {
            if(file_exists(public_path($finance->image))){
                unlink(public_path($finance->image));
            }
            $finance->status_id = 3;
            $finance->save();
            return response()->json(array('success' => true, 'message' => 'Auto Finance Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Auto Finance Not Found?...'));
        }
    }

    public function statusToggle(Request $request)
    {
        $finance = Finance::find($request->id);

        if (!$finance) {
            return response()->json(['error' => 'Finance not found'], 404);
        }
        $finance->status_id = $request->status_id;
        $finance->save();
        return response()->json(['success' => true]);
    }
}
