<?php

namespace App\Http\Controllers\admin\master;

use Illuminate\Http\Request;
use App\Models\Masters\Seller;
use App\Http\Controllers\Controller;
use App\Models\Masters\AutoFuelType;
use Illuminate\Support\Facades\Auth;

class AutoSellersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:auto_seller'])->only('index');
        $this->middleware(['permission:add_auto_seller'])->only(['create','store']);
        $this->middleware(['permission:edit_auto_seller'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_auto_seller'])->only(['destroy']);
    }

    public function index()
    {
        $sellers = Seller::where('status_id',1)->get();
        return view('admin.masters.seller.index', compact('sellers'));
    }

    public function create()
    {
        return view('admin.masters.seller.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'seller_name'   => 'required|string',
            'seller_type'   => 'required|string',
            'location'      => 'required|string',
            'address'       => 'required|string',
            'contact'       => 'required|integer',
        ]);
        
        if(Seller::where('contact',$request->contact)->exists()){
            return redirect()->route('masters.auto-seller.create')->with('error','This Auto Seller Already Exit');
        } else {
            $seller = new Seller();
            $seller->user_id           = Auth::user()->id;
            $seller->seller_name       = $request->seller_name;
            $seller->seller_type       = $request->seller_type;
            $seller->location          = $request->location;
            $seller->address           = $request->address;
            $seller->contact           = $request->contact;
            $seller->save();
            return redirect()->route('masters.auto-seller')->with('success','Auto Seller Successfully Created.');
        }
        
    }

    public function edit($id)
    {
        $seller   = Seller::findOrFail($id);
        return view('admin.masters.seller.edit',compact('seller'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'seller_name'   => 'required|string',
            'seller_type'   => 'required|string',
            'location'      => 'required|string',
            'address'       => 'required|string',
            'contact'       => 'required|integer',
        ]);
        $seller                    = Seller::findOrFail($request->id);
        $seller->seller_name       = $request->seller_name;
        $seller->seller_type       = $request->seller_type;
        $seller->location          = $request->location;
        $seller->address           = $request->address;
        $seller->contact           = $request->contact;
        $seller->save();
        return redirect()->route('masters.auto-seller')->with('success','Auto Seller Successfully Updated.');
    }

    public function destroy(Request $request)
    {
        $seller = Seller::find($request->id);
        if ($seller) {
            $seller->status_id = 2;
            $seller->save();
            return response()->json(array('success' => true, 'message' => 'Auto Seller Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Auto Seller Not Found?...'));
        }
    }
}
