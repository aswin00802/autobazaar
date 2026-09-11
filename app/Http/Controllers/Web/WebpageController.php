<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Legacy account-deletion page, kept because the Play Store listing links to it.
 * Every other legacy customer page now lives in SiteController.
 */
class WebpageController extends Controller
{
    public function accountDelete(){
        return view('web.account-delete');
    }

    public function accountDeleteStore(Request $request){
        $phone = $request->input('phone');

        $user = User::where('phone_number', $phone)->first();

        if ($user) {
            session()->put('success', 'Request sent to admin successfully.your account will be terminated within 24 hours');
            return redirect()->route('account-delete');

        } else {
            session()->put('error', 'User Not Found');
            return redirect()->route('account-delete');
        }


    }
}
