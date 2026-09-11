<?php

namespace App\Http\Controllers\admin\master;

use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:country'])->only('index');
        $this->middleware(['permission:add_country'])->only(['create','store']);
        $this->middleware(['permission:edit_country'])->only(['edit', 'update']);
        // $this->middleware(['permission:delete_country'])->only(['delete']);
    }
    public function index()
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Country','class'=>'active'),
            ),
            'page_head'     =>  "Country",
        );
        $countrys = Country::all();
        return view('admin.masters.country.index',compact('data','countrys'));
    }

    public function create()
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Country Create','class'=>'active'),
            ),
            'page_head'     =>  "Country Create",
        );
        return view('admin.masters.country.create',compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        if(Country::where('name',$request->name)->exists()){
            return redirect()->route('masters.country.create')->with('error','This Country Already Exit');
        } else {
            $country = new Country();
            $country->name          = ucwords($request->name);
            // $country->created_by    = Auth::user()->id;
            // $country->ip_address    = $request->ip();
            $country->save();
            return redirect()->route('masters.country')->with('success','Country Successfully Created.');
        }
    }

    public function edit($id)
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Country Update','class'=>'active'),
            ),
            'page_head'     =>  "Country Update",
        );
        $country = Country::findOrFail($id);
        return view('admin.masters.country.edit',compact('data','country'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        $country                = Country::findOrFail($request->id);
        $country->name          = ucwords($request->name);
        // $country->created_by    = Auth::user()->id;
        // $country->ip_address    = $request->ip();
        $country->save();
        return redirect()->route('masters.country')->with('success','Country Successfully Updated.');
        
    }
}
