<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OTPController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function otpSend(Request $request)
    {
        $request->validate(['identifier' => 'required']);

        $identifier = $request->input('identifier');
        $email = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? $identifier : null;
        $mobile = is_numeric($identifier) ? $identifier : null;

        // 🔹 Check if user exists
        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                return back()->with('error', 'This email is not registered');
            }
        }

        if ($mobile) {
            $user = User::where('mobile', $mobile)->first();
            if (!$user) {
                return back()->with('error', 'This mobile number is not registered');
            }
        }

        // 🔹 Send OTP
        $response = $this->otpService->send('title', 'login_otp', $identifier);

        // 🔹 Handle response
        if ($response['status']) {
            return back()->with('success', $response['message']);
        }

        return back()->with('error', $response['message']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'identifier' => 'required',
            'otp'        => 'required',
        ]);

        // API maadhiri call
        $response = $this->otpService->verify(
            $request->identifier,
            $request->otp,
            5,
            [
                
            ]
        );

        //Web handling
        if ($response['status']) {
            // Example: success-aana OTP verify → dashboard ku redirect
            return redirect()->route('dashboard')->with('success', $response['message']);
        }

        // Failure-na same page ku back
        return back()->withInput()->with('error', $response['message']);
    }

}
