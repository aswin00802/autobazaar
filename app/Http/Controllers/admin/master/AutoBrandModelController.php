<?php

namespace App\Http\Controllers\admin\master;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoModel;
use App\Http\Controllers\Controller;

class AutoBrandModelController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:auto_brand_model'])->only('index');
        $this->middleware(['permission:add_auto_brand_model'])->only(['create','store']);
        $this->middleware(['permission:edit_auto_brand_model'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_auto_brand_model'])->only(['destroy']);
    }

    public function index()
    {
        $auto_models = AutoModel::with('brand')->where('status_id',1)->get();
        return view('admin.masters.model.index', compact('auto_models'));
    }

    public function create()
    {
        $auto_brands = AutoBrand::where('status',1)->get();
        return view('admin.masters.model.create', compact('auto_brands'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'brand_id'      => 'required',
            'model_name'    => 'required'
        ]);
        
        if(AutoModel::where('brand_id',$request->brand_id)->where('model_name',$request->model_name)->exists()){
            return redirect()->route('masters.auto-brands.model.create')->with('error','This Auto Brand Model Already Exit');
        } else {
            $brand = AutoBrand::findOrFail($request->brand_id);
            AutoModel::create([
                'brand_id'      => $request->brand_id,
                'model_name'    => $request->model_name,
                'slug'          => strtolower($brand->brand_name).'/'.Str::slug($request->model_name,'-'),
                'ip_address'    => $request->ip(),
            ]);
            return redirect()->route('masters.auto-brands.model')->with('success','Auto Brand Model Successfully Created.');
        }
        
    }

    public function edit($id)
    {
        $model   = AutoModel::findOrFail($id);
        $auto_brands  = AutoBrand::all();
        return view('admin.masters.model.edit',compact('model','auto_brands'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'brand_id'      => 'required',
            'model_name'    => 'required'
        ]);
        $exitModel              = AutoModel::findOrFail($request->id);
        if(($exitModel->model_name !== $request->model_name) || ((int)$exitModel->brand_id !== (int)$request->brand_id)){
            $brand              = AutoBrand::findOrFail($request->brand_id);
            $exitModel->slug    = Str::lower($brand->brand_name).'/'.Str::slug($request->model_name,'-');
        }
        $exitModel->brand_id    = $request->brand_id;
        $exitModel->model_name  = $request->model_name;
        $exitModel->save();
        return redirect()->route('masters.auto-brands.model')->with('success','Auto Brand Model Successfully Updated.');
    }

    public function destroy(Request $request)
    {
        $brand = AutoModel::find($request->id);
        if ($brand) {
            $brand->status_id = 2;
            $brand->save();
            return response()->json(array('success' => true, 'message' => 'Auto Brand Model Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Auto Brand Model Not Found?...'));
        }
    }
}
