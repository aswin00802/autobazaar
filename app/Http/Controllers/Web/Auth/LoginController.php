<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index()
    {
        // Already signed in: the login form has nothing to offer.
        if (auth()->check()) {
            return redirect()->route('site.account');
        }

        return view('web.auth.login');
    }
}
