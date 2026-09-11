<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function attemptLogin(Request $request)
    {
        $loginField = $request->input('email');
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Find user first
        $user = \App\Models\User::where($fieldType, $loginField)->first();
        
        // If user not found OR inactive, reject
        if (! $user || $user->status != 1) {
            return false;
        }

        // Otherwise attempt login normally
        return $this->guard()->attempt(
            $this->credentials($request),
            // $request->filled('remember')
        );
    }

    protected function credentials(Request $request)
    {
        $loginField = $request->input('email'); // form field name is usually "email"

        // check if input is email or username
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $fieldType => $loginField,
            'password' => $request->input('password'),
        ];
    }


    /**
     * Custom failed response for inactive users
    */
    protected function sendFailedLoginResponse(Request $request)
    {
        $loginField = $request->input('email');
        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = \App\Models\User::where($fieldType, $loginField)->first();

        if ($user && $user->status != 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                $this->username() => ['Your account is inactive. Please contact support.'],
            ]);
        }

        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    public function admin_logout(Request $request)
    {
        Auth::logout(); // default web guard
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully');
    }
}
