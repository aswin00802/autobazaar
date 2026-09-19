<?php

namespace App\Http\Controllers\admin\master;

use Illuminate\Http\Request;
use App\Models\Masters\Seller;
use App\Models\Services\Finance;
use App\Models\FinanceLenderRate;
use App\Http\Controllers\Controller;
use App\Models\Masters\AutoFuelType;
use Illuminate\Support\Facades\Auth;

class AutoFinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:auto_finance'])->only('index');
        $this->middleware(['permission:add_auto_finance'])->only(['create','store']);
        $this->middleware(['permission:edit_auto_finance'])->only(['edit', 'update', 'statusToggle']);
        $this->middleware(['permission:delete_auto_finance'])->only(['destroy']);
    }

    public function index()
    {
        $finances = Finance::where('status_id','!=',3)->latest()->get();
        $rates    = FinanceLenderRate::whereIn('auto_financiar_id', $finances->pluck('id'))->get()->keyBy('auto_financiar_id');
        return view('admin.masters.finance.index', compact('finances','rates'));
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
        ] + $this->rateRules());
        
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
            $this->saveRate($finance->id, $request);
            return redirect()->route('masters.auto-finance')->with('success','Auto Finance Successfully Created.');
        }
        
    }

    public function edit($id)
    {
        $finance   = Finance::findOrFail($id);
        $rate      = FinanceLenderRate::where('auto_financiar_id', $id)->first();
        return view('admin.masters.finance.edit',compact('finance','rate'));
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
        ] + $this->rateRules());
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
        $this->saveRate($finance->id, $request);
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

    /** Loan-term fields shown on the vehicle page "Finance Options" card. */
    private function rateRules()
    {
        return [
            'interest_rate'          => 'nullable|numeric|min:0|max:99.99',
            'min_tenure_months'      => 'nullable|integer|min:1|max:120',
            'max_tenure_months'      => 'nullable|integer|min:1|max:120|gte:min_tenure_months',
            'max_loan_pct'           => 'nullable|integer|min:0|max:100',
            'max_loan_pct_no_cibil'  => 'nullable|integer|min:0|max:100',
            'processing_fee_pct'     => 'nullable|numeric|min:0|max:99.99',
            'documents'              => 'nullable|string',
            'sort_order'             => 'nullable|integer',
        ];
    }

    private function saveRate($financeId, Request $request)
    {
        FinanceLenderRate::updateOrCreate(['auto_financiar_id' => $financeId], [
            'interest_rate'         => $request->filled('interest_rate') ? $request->interest_rate : 11.50,
            'min_tenure_months'     => $request->filled('min_tenure_months') ? (int) $request->min_tenure_months : 12,
            'max_tenure_months'     => $request->filled('max_tenure_months') ? (int) $request->max_tenure_months : 60,
            'max_loan_pct'          => $request->filled('max_loan_pct') ? (int) $request->max_loan_pct : 85,
            'max_loan_pct_no_cibil' => $request->filled('max_loan_pct_no_cibil') ? (int) $request->max_loan_pct_no_cibil : 70,
            'processing_fee_pct'    => $request->filled('processing_fee_pct') ? $request->processing_fee_pct : 0,
            'documents'             => $request->documents,
            'is_featured'           => $request->has('is_featured') ? 1 : 0,
            'sort_order'            => (int) ($request->sort_order ?? 0),
            'status_id'             => 1,
            'created_by'            => Auth::user()->id,
            'ip_address'            => $request->ip(),
        ]);
    }
}
