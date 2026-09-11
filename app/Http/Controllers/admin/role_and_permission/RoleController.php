<?php

namespace App\Http\Controllers\admin\role_and_permission;

use App\Models\SpatieRole;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:roles'])->only('index');
        $this->middleware(['permission:add_roles'])->only(['create','store']);
        $this->middleware(['permission:edit_roles'])->only(['edit', 'update']);
    }

    public function index()
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Role','class'=>'active'),
            ),
            'page_head'     =>  "Role",
        );
        $roles = SpatieRole::all();
        return view('admin.role_has_permission.role.index',compact('data','roles'));
    }

    public function create(Request $request)
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Role Create','class'=>'active'),
            ),
            'page_head'     =>  "Role Create",
        );
        return view('admin.role_has_permission.role.create',compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string|max:255',
        ]);
        if(SpatieRole::where('name',$request->role)->exists()){
            return redirect()->route('roles.create')->with('error','This Role Already Exit');
        } else {
            SpatieRole::create([
                'name' => $request->role,
                'guard_name' => 'web',
            ]);
            return redirect()->route('roles')->with('success','Role Successfully Created.');
        }
    }

    public function edit(Request $request,$id)
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Role Edit','class'=>'active'),
            ),
            'page_head'     =>  "Role Edit",
        );
        $role = SpatieRole::findOrFail($id);
        return view('admin.role_has_permission.role.edit',compact('data','role'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string|max:255',
        ]);
        SpatieRole::where('id',$request->id)->update(['name'=>$request->role]);
        return redirect()->route('roles')->with('success','Role Successfully Updated.');
    }
}
