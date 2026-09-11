<?php

namespace App\Http\Controllers\admin\master;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CityController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:city'])->only('index');
        $this->middleware(['permission:add_city'])->only(['create','store']);
        $this->middleware(['permission:edit_city'])->only(['edit', 'update']);
        // $this->middleware(['permission:delete_city'])->only(['delete']);
    }
    public function index()
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'City','class'=>'active'),
            ),
            'page_head'     =>  "City",
        );
        $citys = City::all();
        return view('admin.masters.city.index',compact('data','citys'));
    }

    public function create()
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'City Create','class'=>'active'),
            ),
            'page_head'     =>  "City Create",
        );
        $states = State::all();
        return view('admin.masters.city.create',compact('data','states'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'state_id'    => 'required',
            'name'          => 'required|string|max:255',
        ]);
        if(City::where('state_id',$request->state_id)->where('name',$request->name)->exists()){
            return redirect()->route('masters.city.create')->with('error','This City Already Exit');
        } else {
            $city = new City();
            $city->state_id    = $request->state_id;
            $city->name          = ucwords($request->name);
            $city->created_by    = Auth::user()->id;
            $city->ip_address    = $request->ip();
            $city->save();
            return redirect()->route('masters.city')->with('success','City Successfully Created.');
        }
    }

    public function edit($id)
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'City Update','class'=>'active'),
            ),
            'page_head'     =>  "City Update",
        );
        $states = State::all();
        $city = City::findOrFail($id);
        return view('admin.masters.city.edit',compact('data','city','states'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'state_id'    => 'required',
            'name'          => 'required|string|max:255',
        ]);
        $city                = City::findOrFail($request->id);
        $city->state_id    = $request->state_id;
        $city->name          = ucwords($request->name);
        $city->created_by    = Auth::user()->id;
        $city->ip_address    = $request->ip();
        $city->save();
        return redirect()->route('masters.city')->with('success','City Successfully Updated.');
        
    }
}
