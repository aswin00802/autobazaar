<?php

namespace App\Http\Controllers\admin\service;

use Illuminate\Http\Request;
use App\Models\Services\Mechanic;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class MechanicController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:mechanic'])->only('index');
        $this->middleware(['permission:add_mechanic'])->only(['create','store']);
        $this->middleware(['permission:edit_mechanic'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_mechanic'])->only(['destroy']);
    }

    public function index()
    {
        $mechanics = Mechanic::where('status_id',1)->get();
        return view('admin.service.mechanic.index', compact('mechanics'));
    }

    public function create()
    {
        return view('admin.service.mechanic.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'shop_name' =>'required',
            'location'  =>'required',
            'address'   =>'required',
            'contact'   =>'required',
            'alternate' =>'nullable',
            'category'  =>'required',
        ]);
        
        if(Mechanic::where('shop_name',$request->shop_name)->exists()){
            return redirect()->route('services.mechanic.create')->with('error','This Auto Mechanic Already Exit');
        } else {
            $mechanic = new Mechanic();
            $mechanic->user_id     = Auth::user()->id;
            $mechanic->shop_name   = $request->shop_name;
            $mechanic->address     = $request->address;
            $mechanic->location    = $request->location;
            $mechanic->contact     = $request->contact;
            $mechanic->alternate   = $request->alternate;
            $mechanic->category    = $request->category;
            $mechanic->save();
            return redirect()->route('services.mechanic')->with('success','Auto Mechanic Successfully Created.');
        }
        
    }

    public function edit($id)
    {
        $mechanic   = Mechanic::findOrFail($id);
        return view('admin.service.mechanic.edit',compact('mechanic'));
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'shop_name' =>'required',
            'location'  =>'required',
            'address'   =>'required',
            'contact'   =>'required',
            'alternate' =>'nullable',
            'category'  =>'required',
        ]);
        $mechanic              = Mechanic::findOrFail($request->id);
        $mechanic->shop_name   = $request->shop_name;
        $mechanic->address     = $request->address;
        $mechanic->location    = $request->location;
        $mechanic->contact     = $request->contact;
        $mechanic->alternate   = $request->alternate;
        $mechanic->category    = $request->category;
        $mechanic->save();
        return redirect()->route('services.mechanic')->with('success','Auto Mechanic Successfully Updated.');
    }

    public function destroy(Request $request)
    {
        $mechanic = Mechanic::find($request->id);
        if ($mechanic) {
            $mechanic->status_id = 2;
            $mechanic->save();
            return response()->json(array('success' => true, 'message' => 'Auto Mechanic Deleted Successfully'));
        } else {
            return response()->json(array('success' => false, 'message' => 'Auto Mechanic Not Found?...'));
        }
    }
}
