<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Models\AutoOtp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class OtpController extends Controller
{
    /** An OTP is good for this long after it is sent. */
    private const OTP_VALID_MINUTES = 10;

    /** Wrong guesses allowed per OTP before it is thrown away. */
    private const OTP_MAX_ATTEMPTS = 5;

    public function Otp_page(Request $request,$mobile,$otp_type)
    {
        $phone_number = $mobile;
        $type         = $otp_type;
        
        return view('web.auth.otp', compact('phone_number', 'type'));
    }

    public function sendOTP(Request $request)
    {
        if ($request->type == 'register') {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|digits:10',
                'name'         => 'required|string',
                'auto_area_id' => 'required|numeric',
                'type'         => 'required|in:login,register',
            ]);
        } else if ($request->type == 'login') {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|digits:10',
                'type'         => 'required|in:login,register',
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Please Fill all Inputs']);
        }

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please Fill all Inputs']);
        }

        $type = $request->type;

        // Check if user already registered or new user
        if ($type == 'register') {
            if (User::where('phone_number', $request->phone_number)->exists()) {
                return response()->json(['success' => false, 'message' => 'Phone number is already registered. Please login.']);
            } else {
                User::create([
                    'name'         => $request->name,
                    'phone_number' => $request->phone_number,
                    'auto_area_id' => $request->auto_area_id,
                ]);
            }
        }

        // check login user exit or not
        if ($type == 'login') {
            $user = User::where('phone_number', $request->phone_number)->exists();
            if (! $user) {
                return response()->json(['success' => false, 'message' => 'Not registered. please register']);
            }
        }

        // Generate OTP
        $otp = rand(1000, 9999);

        // Send SMS
        $sms    = "Dear Customer, $otp is your verification code - PNGOTP";
        $url    = 'http://site.ping4sms.com/api/smsapi';
        $params = [
            // Set PING4SMS_KEY in .env; the fallback keeps SMS working until then.
            'key'        => config('services.ping4sms.key', '0bc8ea57e5adc287cc8d163c82693450'),
            'route'      => 2,
            'sender'     => 'PNGOTP',
            'number'     => $request->phone_number,
            'sms'        => $sms,
            'templateid' => '1507165967974501361',
        ];

        try {
            $client = new \GuzzleHttp\Client();
            $response =  $client->post($url, ['form_params' => $params]);
            // $raw = (string) $response->getBody();
            // \Log::info('SMS API RESPONSE', [
            //     'status_code' => $response->getStatusCode(),
            //     'body'        => $raw,
            // ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send OTP.']);
        }

        // Save or update OTP
        $existingOtp = AutoOtp::where('phone', $request->phone_number)->first();
        if ($existingOtp) {
            $existingOtp->otp        = $otp;
            $existingOtp->status     = 'pending';
            $existingOtp->updated_at = now();
            $existingOtp->save();
            Cache::forget('otp_fail:' . $request->phone_number);
        } else {
            AutoOtp::create([
                'phone'      => $request->phone_number,
                'otp'        => $otp,
                'status'     => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Cache::forget('otp_fail:' . $request->phone_number);

        return response()->json(['success' => true, 'message' => 'send OTP Successfully.', 'url' => 'user.otp.send', 'mobile' => $request->phone_number, 'otp_type' => $type]);
    }

    public function verifyOTP(Request $request)
    {
        //\Log::info($request);exit;
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|digits:10',
            'type'         => 'required|in:login,register',
            'otp'          => 'required|digits:4',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Please Fill OTP']);
        }
        $phone   = $request->phone_number;
        $otp     = $request->otp;
        $type    = $request->type;
        $autoOtp = AutoOtp::where('phone', $phone)->latest()->first();

        // An OTP works once, for a few minutes, and only survives a handful of
        // wrong guesses — otherwise a 4-digit code can simply be brute-forced.
        $failKey = 'otp_fail:' . $phone;
        $sentAt  = $autoOtp?->updated_at ?? $autoOtp?->created_at;

        if (! $autoOtp
            || $autoOtp->status !== 'pending'
            || ! $sentAt
            || Carbon::parse($sentAt)->lt(now()->subMinutes(self::OTP_VALID_MINUTES))
        ) {
            return response()->json(['success' => false, 'message' => 'OTP expired. Please request a new OTP.']);
        }

        if ((int) Cache::get($failKey, 0) >= self::OTP_MAX_ATTEMPTS) {
            $autoOtp->status = 'expired';
            $autoOtp->save();

            return response()->json(['success' => false, 'message' => 'Too many wrong attempts. Please request a new OTP.']);
        }

        if ((string) $autoOtp->otp !== (string) $otp) {
            Cache::put($failKey, (int) Cache::get($failKey, 0) + 1, now()->addMinutes(self::OTP_VALID_MINUTES));

            return response()->json(['success' => false, 'message' => 'Invalid Otp']);
        }

        Cache::forget($failKey);

        $user = User::where('phone_number', $phone)->first();
        if ($user) {
            $user->otp = $otp;
            $user->save();

            $autoOtp->status = 'verified';
            $autoOtp->save();

            // Capture these BEFORE Auth::login(), which regenerates the session
            // id and clears the intended URL along with it.
            $guestSessionId = session()->getId();
            $intended       = session()->pull('url.intended');

            if ($type == 'register') {
                Auth::login($user);
                $this->afterLogin($user, $guestSessionId);
                return response()->json(['success' => true, 'message' => 'OTP Successfully Verified.Please Login.', 'redirect_url' => $intended ?: route('site.home')]);
            } else if ($type == 'login') {
                Auth::login($user);
                $this->afterLogin($user, $guestSessionId);
                return response()->json(['success' => true, 'message' => 'OTP Successfully Verified.', 'redirect_url' => $intended ?: route('site.home')]);
                // $credentials = $request->only('phone_number');
                // if (Auth::attempt($credentials)) {
                //     return response()->json(['success' => true, 'message' => 'OTP Successfully Verified.', 'redirect_url' => route('site.home')]);
                // } else {
                //     return response()->json(['success' => 'invalid', 'message' => 'Invalid Details.', 'redirect_url' => route('login')]);
                // }
            }
        } else {
            return response()->json(['success' => false, 'message' => 'User not registered. Please register first.']);
        }
    }

    /**
     * Carry anything the visitor did as a guest across into their account.
     * Right now that means the accessories cart, so nothing is lost at sign-in.
     */
    private function afterLogin($user, string $guestSessionId): void
    {
        try {
            app(\App\Services\CartService::class)->mergeGuestCart((int) $user->id, $guestSessionId);
        } catch (\Throwable $e) {
            // Never block a successful login because a cart merge failed.
            \Log::warning('Guest cart merge failed: ' . $e->getMessage());
        }
    }
}
