<?php

namespace App\Http\Controllers\admin\user_management;

use App\Models\User;
use App\Models\Auto\Auto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class UserPostAutoController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user_post_auto_list'])->only(['index']);
        $this->middleware(['permission:user_post_auto_approval'])->only(['approval']);
    }
    public function index()
    {
        $used_autos = Auto::with(['autoBrands','autoFuelType', 'AutoPriceRange','cities', 'autoOwners'])
        ->where('post_type','U')->where('auto_usage_status','used_auto')->where('status','=',2)->orderBy('status')->get();
        return view('admin.user_management.user_post_auto',compact('used_autos'));
    }

    public function approval(Request $request)
    {
        $auto = Auto::findOrFail($request->id);
        $auto->status = $request->status;
        if($request->status == 1){
            $auto->auto_status = 'active';
        } else if($request->status == 3){
            $auto->auto_status = 'rejected';
        }
        if(isset($request->remark)){
            $auto->remark = $request->remark;
        }
        $auto->save();
        return redirect()->route('user-management.users-post-auto-list')->with('success','User Post Auto Approval Successfully Submitted!');
    }

    public function user_info($id)
    {
        // $userId = Crypt::decryptString($id);
        // $user = User::findOrFail($userId);
        // $autos = Auto::where('user_id',$userId)->get();
        // return view('admin.user_management.user_info',compact('user','autos'));
    }
}
