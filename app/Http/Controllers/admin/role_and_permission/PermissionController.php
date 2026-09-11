<?php

namespace App\Http\Controllers\admin\role_and_permission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:permissions'])->only('index');
        $this->middleware(['permission:add_permissions'])->only(['create','store']);
        $this->middleware(['permission:edit_permissions'])->only(['edit', 'update']);
        // $this->middleware(['permission:delete_permissions'])->only(['delete']);
    }

    public function index()
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Permission','class'=>'active'),
            ),
            'page_head'     =>  "Permission",
        );
        $permissions = Permission::all();
        return view('admin.role_has_permission.permission.index',compact('data','permissions'));
    }

    public function create(Request $request)
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Permission Create','class'=>'active'),
            ),
            'page_head'     =>  "Permission Create",
        );
        return view('admin.role_has_permission.permission.create',compact('data'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'permission'    => 'required|string|max:255',
            'group'         => 'required|string|max:255',
        ]);
        if(Permission::where('name',$request->permission)->exists()){
            return redirect()->route('permissions.create')->with('error','This Permissions Already Exit');
        } else {
            Permission::create([
                'name' => $request->permission,
                'group_name'=>$request->group,
                'guard_name' => 'web',
            ]);
            return redirect()->route('permissions')->with('success','Permissions Successfully Created.');
        }
    }

    public function edit(Request $request,$id)
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Permission Edit','class'=>'active'),
            ),
            'page_head'     =>  "Permission Edit",
        );
        $permission = Permission::findOrFail($id);
        return view('admin.role_has_permission.permission.edit',compact('data','permission'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'permission'    => 'required|string|max:255',
            'group'         => 'required|string|max:255',
        ]);
        Permission::where('id',$request->id)->update(['name'=>$request->permission,'group_name'=>$request->group]);
        return redirect()->route('permissions')->with('success','Permission Successfully Updated.');
    }
}
