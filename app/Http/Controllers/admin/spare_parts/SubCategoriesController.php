<?php

namespace App\Http\Controllers\admin\spare_parts;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\spareparts\SparepartsCategories;
use App\Models\spareparts\SparepartsSubCategories;

class SubCategoriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:sparepart_subcategories'])->only(['index']);
        $this->middleware(['permission:add_sparepart_subcategories'])->only(['create','store']);
        $this->middleware(['permission:edit_sparepart_subcategories'])->only(['edit','update']);
        $this->middleware(['permission:delete_sparepart_subcategories'])->only(['delete']);
    
    }
    public function index()
    {
        $subcategories = SparepartsSubCategories::where('status_id',1)->get(['id','category_id','name','image']);
        return view('admin.spare_parts.sub_category.index',compact('subcategories'));
    }

    public function create()
    {
        $categories = SparepartsCategories::where('status_id',1)->get(['id','name']);
        return view('admin.spare_parts.sub_category.create',compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|integer',
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'image'         => 'nullable|file|image|mimes:jped,png,jpg'
        ]);
        if(SparepartsSubCategories::where('category_id',$request->category_id)->where('name',$request->name)->exists()){
            return redirect()->route('spare-parts.sub-categories.create')->with('error','This SubCategories Already Exit');
        } else {
            $exitCount = SparepartsSubCategories::latest()->first();
            if($exitCount){
                $fileNameCreate = $exitCount->id + 1;
            } else {
                $fileNameCreate = 1;
            }
            $subcategories                 = new SparepartsSubCategories();
            $subcategories->category_id    = $request->category_id;
            $subcategories->name           = $request->name;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $subcategories->image = uploadedAsset($request, 'image', 'subcategories'.$fileNameCreate, 'spareparts/subcategories');
            }
            $subcategories->description    = $request->description;
            $subcategories->slug           = Str::slug($request->name,'-');
            $subcategories->created_by     = Auth::user()->id;
            $subcategories->ip_address     = $request->ip();
            $subcategories->save();
            return redirect()->route('spare-parts.sub-categories')->with('success','This SubCategories Created Successfully');
        }
    }

    public function edit($id)
    {
        $categories = SparepartsCategories::where('status_id',1)->get(['id','name']);
        $subcategories = SparepartsSubCategories::findOrFail($id);
        return view('admin.spare_parts.sub_category.edit',compact('subcategories','categories'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'category_id'   => 'required|integer',
            'name'          => 'required|string',
            'description'   => 'nullable|string',
            'image'         => 'nullable|file|image|mimes:jped,png,jpg'
        ]);
        $subcategories = SparepartsSubCategories::findOrFail($request->id);
        if(($subcategories->name != $request->name) && ($subcategories->category_id != $request->category_id)){
            if(SparepartsSubCategories::where('category_id',$request->category_id)->where('name',$request->name)->exists()){
                return redirect()->route('spare-parts.sub-categories.edit',$request->id)->with('error','This SubCategories Already Exit');
            }
        } else {
            if(($subcategories->name !== $request->name) ){
                $subcategories->slug    = Str::slug($request->name,'-');
            }
            $subcategories->category_id    = $request->category_id;
            $subcategories->name           = $request->name;
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $subcategories->image = uploadedAsset($request, 'image', 'subcategories'.$subcategories->id, 'spareparts/subcategories');
            }
            $subcategories->description    = $request->description;
            
            $subcategories->created_by     = Auth::user()->id;
            $subcategories->ip_address     = $request->ip();
            $subcategories->save();
            return redirect()->route('spare-parts.sub-categories')->with('success','This SubCategories Updated Successfully');
        }
    }

    public function delete(Request $request)
    {
        $categorie = SparepartsSubCategories::find($request->id);
        if ($categorie) {
            $categorie->status_id      = 2;
            $categorie->created_by     = Auth::user()->id;
            $categorie->ip_address     = $request->ip();
            $categorie->save();
            return response()->json(array('success' => true, 'message' => 'Spareparts SubCategories Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Spareparts SubCategories Not Found?...'));
        }
    }
}
