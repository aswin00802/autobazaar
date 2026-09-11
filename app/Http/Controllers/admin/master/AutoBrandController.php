<?php

namespace App\Http\Controllers\admin\master;

use Illuminate\Http\Request;
use App\Models\Masters\AutoBrand;
use App\Http\Controllers\Controller;

class AutoBrandController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:auto_brand'])->only('index');
        $this->middleware(['permission:add_auto_brand'])->only(['create','store']);
        $this->middleware(['permission:edit_auto_brand'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_auto_brand'])->only(['destroy']);
    }

    public function index()
    {
        $auto_brands = AutoBrand::where('status',1)->get();
        return view('admin.masters.brand.index', compact('auto_brands'));
    }

    public function create()
    {
        $auto_brands = AutoBrand::all();
        return view('admin.masters.brand.create', compact('auto_brands'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'brand_name' => 'required|string|max:255',
            'brand_image' => 'nullable|file'
        ]);
        unset($validatedData['brand_image']);
        
        if(AutoBrand::where('brand_name',$validatedData['brand_name'])->exists()){
            return redirect()->route('masters.auto-brands.create')->with('error','This Auto Brand Already Exit');
        } else {
            if ($request->hasFile('brand_image') && $request->file('brand_image')->isValid()) {
                $profile = uploadedAsset($request, 'brand_image', 'brand_image_'.$request->brand_name, 'brands');
            }
            $validatedData['status'] = 1;
            $validatedData['profile'] =  $profile;
            $auto = AutoBrand::create($validatedData);
            if($auto) {
                return redirect()->route('masters.auto-brands')->with('success','Auto Brand Successfully Created.');
            } else {
                return redirect()->route('masters.auto-brands.create')->with('error','This Auto Brand Not Create Please Try Again');
            }
        }
    }

    public function edit($id)
    {
        $auto_brand = AutoBrand::findOrFail($id);
        return view('admin.masters.brand.edit', compact('auto_brand'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'brand_name' => 'required|string|max:255',
            'brand_image' => 'nullable|file'
        ]);
        // unset($validatedData['brand_image']);
        $brand = AutoBrand::findOrFail($request->id);
        $brand->brand_name = $request->brand_name;
        if ($request->hasFile('brand_image') && $request->file('brand_image')->isValid()) {
            $brand->profile = uploadedAsset($request, 'brand_image', 'brand_image_'.$request->brand_name, 'brands');
        }
        $brand->save();
        return redirect()->route('masters.auto-brands')->with('success','Auto Brand Successfully Updated.');
    }

    public function destroy(Request $request)
    {
        $brand = AutoBrand::find($request->id);
        if ($brand) {
            $brand->status = 2;
            $brand->save();
            return response()->json(array('success' => true, 'message' => 'Auto Brand Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Auto Brand Not Found?...'));
        }
    }
}
