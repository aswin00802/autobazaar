<?php

namespace App\Http\Controllers\admin\user_management;

use App\Models\User;
use App\Models\Auto\Auto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class UserListController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:user_list'])->only(['index']);
        $this->middleware(['permission:user_info'])->only(['user_info']);
    }
    public function index()
    {
        $admin = User::where('phone_number','9751535189')->first();
        $playstore_test = User::where('phone_number','9094262603')->first();
        $users = User::with(['userInfo', 'autoAreas'])
                ->where('id', '!=', $admin->id)
                ->where('id', '!=', $playstore_test->id)
                ->orderBy('created_at', 'desc')
                ->get();
        return view('admin.user_management.userlist',compact('admin','playstore_test','users'));
    }

    public function user_info($id)
    {
        $userId = Crypt::decryptString($id);
        $user = User::findOrFail($userId);
        $autos = Auto::where('user_id',$userId)->get();
        return view('admin.user_management.user_info',compact('user','autos'));
    }
}
