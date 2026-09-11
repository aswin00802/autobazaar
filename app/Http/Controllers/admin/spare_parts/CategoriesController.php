<?php

namespace App\Http\Controllers\admin\spare_parts;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\spareparts\SparepartsCategories;

class CategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:sparepart_categories'])->only(['index']);
        $this->middleware(['permission:add_sparepart_categories'])->only(['create','store']);
        $this->middleware(['permission:edit_sparepart_categories'])->only(['edit','update']);
        $this->middleware(['permission:delete_sparepart_categories'])->only(['delete']);
    
    }
    public function index()
    {
        $categories = SparepartsCategories::where('status_id',1)->get(['id','name','image']);
        return view('admin.spare_parts.category.index',compact('categories'));
    }

    public function create()
    {
        return view('admin.spare_parts.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'image'         => 'nullable|file|image|mimes:jped,png,jpg'
        ]);
        if(SparepartsCategories::where('name',$request->name)->exists()){
            return redirect()->route('spare-parts.categories.create')->with('error','This Categories Already Exit');
        } else {
            $exitCount = SparepartsCategories::latest()->first();
            if($exitCount){
                $fileNameCreate = $exitCount->id + 1;
            } else {
                $fileNameCreate = 1;
            }
            $categories                 = new SparepartsCategories();
            $categories->name           = $request->name;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $categories->image = uploadedAsset($request, 'image', 'categories'.$fileNameCreate, 'spareparts/categories');
            }
            $categories->description    = $request->description;
            $categories->created_by     = Auth::user()->id;
            $categories->ip_address     = $request->ip();
            $categories->save();
            return redirect()->route('spare-parts.categories')->with('success','This Categories Created Successfully');
        }
    }

    public function edit($id)
    {
        $categories = SparepartsCategories::findOrFail($id);
        return view('admin.spare_parts.category.edit',compact('categories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'image'         => 'nullable|file|image|mimes:jped,png,jpg'
        ]);
        $categories = SparepartsCategories::findOrFail($request->id);
        if($categories->name != $request->name){
            if(SparepartsCategories::where('name',$request->name)->exists()){
                return redirect()->route('spare-parts.categories.edit',$request->id)->with('error','This Categories Already Exit');
            }
        } else {
            $categories->name           = $request->name;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $categories->image = uploadedAsset($request, 'image', 'categories'.$categories->id, 'spareparts/categories');
            }
            $categories->description    = $request->description;
            $categories->created_by     = Auth::user()->id;
            $categories->ip_address     = $request->ip();
            $categories->save();
            return redirect()->route('spare-parts.categories')->with('success','This Categories Updated Successfully');
        }
    }

    public function delete(Request $request)
    {
        $categorie = SparepartsCategories::find($request->id);
        if ($categorie) {
            $categorie->status_id      = 2;
            $categorie->created_by     = Auth::user()->id;
            $categorie->ip_address     = $request->ip();
            $categorie->save();
            return response()->json(array('success' => true, 'message' => 'Spareparts Categories Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Spareparts Categories Not Found?...'));
        }
    }
}
