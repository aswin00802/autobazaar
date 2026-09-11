<?php

namespace App\Http\Controllers\admin\role_and_permission;

use App\Models\SpatieRole;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class RolehaPermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:role_has_permission'])->only('index');
        $this->middleware(['permission:edit_role_has_permission'])->only(['edit', 'update']);
    }

    public function index()
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Role has Permission','class'=>'active'),
            ),
            'page_head'     =>  "Role has Permission",
        );
        $roles = SpatieRole::all();

        return view('admin.role_has_permission.role_and_permission.index',compact('data','roles'));
    }

    public function edit(Request $request,$id)
    {
        $data = array(
            'breadcrumbs'   => array(
                array('text'=>'Home','href'=>url('dashboard'),'class'=>''),
                array('text'=>'Role has Permission Update','class'=>'active'),
            ),
            'page_head'     =>  "Role has Permission Update",
        );
        $role = SpatieRole::findOrFail($id);
        $permission_groups = Permission::all()->groupBy('group_name');

        return view('admin.role_has_permission.role_and_permission.edit',compact('data','role','permission_groups'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'permissions'   => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'permissions.required' => 'Please select at least one permission',
            'permissions.*.exists' => 'Invalid permission selected',
        ]);

        $role = SpatieRole::findOrFail($request->id);
        $role->name = $request->role;
        // $role->syncPermissions($request->permissions);
        $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();

        $role->syncPermissions($permissions);
        $role->save();
        return redirect()->route('role_has_permission')->with('success','Permission Successfully Assigned the '.$role->name.' Role.');
    }
}
