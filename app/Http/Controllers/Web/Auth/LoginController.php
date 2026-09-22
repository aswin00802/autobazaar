<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\Auth\Concerns\RendersAuthPage;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use RendersAuthPage;

    public function index()
    {
        // Already signed in: the login form has nothing to offer.
        if (auth()->check()) {
            return redirect()->route('site.account');
        }

        return $this->authView('login');
    }
}
