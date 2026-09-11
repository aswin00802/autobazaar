<?php

namespace App\Http\Controllers\admin\master;

use App\Http\Controllers\Controller;
use App\Models\Masters\AuthorizedSeller;
use App\Models\Masters\AutoBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorizedAutoSellerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:authorized_seller'])->only('index');
        $this->middleware(['permission:add_authorized_seller'])->only(['create','store']);
        $this->middleware(['permission:edit_authorized_seller'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_authorized_seller'])->only(['destroy']);
    }

    public function index()
    {
        $sellers = AuthorizedSeller::with('brand:id,brand_name')->where('status_id','!=',3)->get();
        return view('admin.masters.authorized_seller.index', compact('sellers'));
    }

    public function create()
    {
        $brands = AutoBrand::where('status',1)->get(['id','brand_name']);
        return view('admin.masters.authorized_seller.create',compact('brands'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'brand'         => 'required',
            'seller_name'   => 'required|string',
            'seller_type'   => 'required|string',
            'location'      => 'required|string',
            'address'       => 'required|string',
            'contact'       => 'required|integer',
            'image'         => 'nullable|file',
        ]);
        
        if(AuthorizedSeller::where('contact',$request->contact)->where('brand_id',$request->brand)->exists()){
            return redirect()->route('masters.auto-authorized-seller.create')->with('error','This Auto Seller Already Exit');
        } else {
            $seller = new AuthorizedSeller();
            $seller->user_id           = Auth::user()->id;
            $seller->brand_id          = $request->brand;
            $seller->dealer_name       = $request->seller_name;
            $seller->dealer_type       = $request->seller_type;
            $seller->location          = $request->location;
            $seller->address           = $request->address;
            $seller->contact           = $request->contact;
            if ($request->hasFile('seller_image')) {
                $seller->image = uploadedAsset($request, 'seller_image', 'seller_image'.time(), 'authorize_seller');
            }
            $seller->ip_address         = $request->ip();
            $seller->save();
            return redirect()->route('masters.auto-authorized-seller')->with('success','Auto Seller Successfully Created.');
        }
        
    }

    public function edit($id)
    {
        $seller   = AuthorizedSeller::findOrFail($id);
        $brands = AutoBrand::where('status',1)->get(['id','brand_name']);
        return view('admin.masters.authorized_seller.edit',compact('seller','brands'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'brand'         => 'required',
            'seller_name'   => 'required|string',
            'seller_type'   => 'required|string',
            'location'      => 'required|string',
            'address'       => 'required|string',
            'contact'       => 'required|integer',
            'image'         => 'nullable|file',
        ]);
        $seller                    = AuthorizedSeller::findOrFail($request->id);
        $seller->brand_id          = $request->brand;
        $seller->dealer_name       = $request->seller_name;
        $seller->dealer_type       = $request->seller_type;
        $seller->location          = $request->location;
        $seller->address           = $request->address;
        $seller->contact           = $request->contact;
        if ($request->hasFile('seller_image')) {
            if(isset($seller->image)){
                if(file_exists(public_path($seller->image))){
                    unlink(public_path($seller->image));
                }
            }
            $seller->image = uploadedAsset($request, 'seller_image', 'seller_image'.time(), 'authorize_seller');
        }
        $seller->ip_address         = $request->ip();
        $seller->save();
        return redirect()->route('masters.auto-authorized-seller')->with('success','Auto Seller Successfully Updated.');
    }

    public function destroy(Request $request)
    {
        $seller = AuthorizedSeller::find($request->id);
        if ($seller) {
            $seller->status_id = 3;
            $seller->save();
            return response()->json(array('success' => true, 'message' => 'Auto Seller Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Auto Seller Not Found?...'));
        }
    }
}
