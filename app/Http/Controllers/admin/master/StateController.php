<?php

namespace App\Http\Controllers\admin\master;

use App\Models\State;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class StateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:state'])->only('index');
        $this->middleware(['permission:add_state'])->only(['create','store']);
        $this->middleware(['permission:edit_state'])->only(['edit', 'update']);
        // $this->middleware(['permission:delete_state'])->only(['delete']);
    }
    public function index(Request $request)
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'State','class'=>'active'),
            ),
            'page_head'     =>  "State",
        );

        // 4,092 states, and the country name used to be a separate query per row.
        // with() fetches the countries in one go; per_page decides how many rows
        // reach the browser (config/admin_lists.php, 0 = all of them).
        $query = State::with('country:id,name')->orderBy('name')->orderBy('id');

        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('country', fn ($c) => $c->where('name', 'like', "%{$search}%"));
            });
        }

        $perPage = (int) config('admin_lists.per_page.state', 50);
        $states = $perPage > 0 ? $query->paginate($perPage)->withQueryString() : $query->get();

        return view('admin.masters.state.index',compact('data','states'));
    }

    public function create()
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'State Create','class'=>'active'),
            ),
            'page_head'     =>  "State Create",
        );
        $countrys = Country::all();
        return view('admin.masters.state.create',compact('data','countrys'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'country_id'    => 'required',
            'name'          => 'required|string|max:255',
        ]);
        if(State::where('country_id',$request->country_id)->where('name',$request->name)->exists()){
            return redirect()->route('masters.state.create')->with('error','This State Already Exit');
        } else {
            $state = new State();
            $state->country_id    = $request->country_id;
            $state->name          = ucwords($request->name);
            // $state->created_by    = Auth::user()->id;
            // $state->ip_address    = $request->ip();
            $state->save();
            return redirect()->route('masters.state')->with('success','State Successfully Created.');
        }
    }

    public function edit($id)
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'State Update','class'=>'active'),
            ),
            'page_head'     =>  "State Update",
        );
        $countrys = Country::all();
        $state = State::findOrFail($id);
        return view('admin.masters.state.edit',compact('data','countrys','state'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'country_id'    => 'required',
            'name'          => 'required|string|max:255',
        ]);
        $state                = State::findOrFail($request->id);
        $state->country_id    = $request->country_id;
        $state->name          = ucwords($request->name);
        // $state->created_by    = Auth::user()->id;
        // $state->ip_address    = $request->ip();
        $state->save();
        return redirect()->route('masters.state')->with('success','State Successfully Updated.');
        
    }
}
