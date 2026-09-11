<?php

namespace App\Http\Controllers\admin\master;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Masters\AutoFuelType;

class AutoFuelTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:auto_fuel_type'])->only('index');
        $this->middleware(['permission:add_auto_fuel_type'])->only(['create','store']);
        $this->middleware(['permission:edit_auto_fuel_type'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_auto_fuel_type'])->only(['destroy']);
    }

    public function index()
    {
        $fuel_types = AutoFuelType::where('status',1)->get();
        return view('admin.masters.fueltype.index', compact('fuel_types'));
    }

    public function create()
    {
        $auto_fuels = AutoFuelType::where('status',1)->get();
        return view('admin.masters.fueltype.create', compact('auto_fuels'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'    => 'required',
            'price'   => 'nullable',  
        ]);
        
        if(AutoFuelType::where('name',$request->name)->exists()){
            return redirect()->route('masters.auto-fueltype.create')->with('error','This Auto Fuel Type Already Exit');
        } else {
            AutoFuelType::create([
                'name'      => $request->name,
                'price'     => $request->price,
                'status'    => 1,
            ]);
            return redirect()->route('masters.auto-fueltype')->with('success','Auto Fuel Type Successfully Created.');
        }
        
    }

    public function edit($id)
    {
        $fueltype   = AutoFuelType::findOrFail($id);
        return view('admin.masters.fueltype.edit',compact('fueltype'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'name'    => 'required',
            'price'   => 'nullable',  
        ]);
        $autofuel        = AutoFuelType::findOrFail($request->id);
        $autofuel->name  = $request->name;
        $autofuel->price = $request->price;
        $autofuel->save();
        return redirect()->route('masters.auto-fueltype')->with('success','Auto Fuel Type Successfully Updated.');
    }

    public function destroy(Request $request)
    {
        $fueltype = AutoFuelType::find($request->id);
        if ($fueltype) {
            $fueltype->status = 2;
            $fueltype->save();
            return response()->json(array('success' => true, 'message' => 'Auto Fuel Type Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Auto Fuel Type Not Found?...'));
        }
    }
}
