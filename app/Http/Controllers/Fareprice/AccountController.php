<?php

namespace App\Http\Controllers\Fareprice;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function farepriceAccountDelete(){
        return view('fareprice.account-delete');
    }

    public function farepriceaccountDeleteStore(Request $request){
        $phone = $request->input('phone');
        $user = Customer::where('phone', $phone)->first();
        
        if ($user) {
            // session()->put('success', 'Request sent to admin successfully.your account will be terminated within 24 hours');
            // return redirect()->route('fareprice.account-delete');
            return back()->with('success', 'Request sent to admin successfully.your account will be terminated within 24 hours');

        } else {
            // session()->put('error', 'User Not Found');
            // return redirect()->route('fareprice.account-delete');
            return back()->with('error', 'User Not Found');
        }


    }

    public function farepricePrivacyPolicy()
    {
        return view('fareprice.privacy-policy');
    }

    public function farepriceTermsCondition()
    {
        return view('fareprice.terms-condition');
    }
}
