<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use GuzzleHttp\Client;
use App\Models\AutoOtp;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OTPAuthController extends Controller
{
    // protected $otpService;

    // public function __construct(OtpService $otpService)
    // {
    //     $this->otpService = $otpService;
    // }

    // public function sendOtp(Request $request)
    // {

    //     $request->validate(['identifier' => 'required']);
    //     $identifier = $request->input('identifier');
    //     $email = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? $identifier : null;
    //     $mobile = is_numeric($identifier) ? $identifier : null;
    //     if(isset($email)){
    //         $user = User::where('email',$email)->first();
    //         if(!$user){
    //             return [
    //                 'status'  => false,
    //                 'message' => 'This email is not registered'
    //             ];
    //         }
    //     }
    //     if(isset($mobile)){
    //         $user = User::where('mobile',$mobile)->first();
    //         if(!$user){
    //             return [
    //                 'status'  => false,
    //                 'message' => 'This Mobile No is not registered'
    //             ];
    //         }
    //     }
    //     $response = $this->otpService->send('title','login_otp',$identifier);

    //     // $response = $this->otpService->send($request->identifier);
    //     return response()->json($response, $response['status'] ? 200 : 429);
    // }

    // public function verifyOtp(Request $request)
    // {
    //     $request->validate([
    //         'identifier' => 'required',
    //         'otp' => 'required',
    //         // 'fcm_token' => 'required',
    //         // 'device_id' => 'required',
    //     ]);

    //     // $response = $this->otpService->verify($request->identifier, $request->otp);
    //     $response = $this->otpService->verify($request->identifier, $request->otp,5,[
    //         // 'fcm_token'   => $request->fcm_token,
    //         // 'device_id'  => $request->device_id,
    //     ]);
    //     return response()->json($response, $response['status'] ? 200 : 422);
    // }

    public function sendOtp(Request $request)
    {
        // Validate the phone number input and type
        $validatedData = $request->validate([
            'phone' => 'required|integer',
            'type' => 'required|string|in:register,login',  // Ensure 'type' is either 'register' or 'login'
            // 'name'          => 'nullable|string|max:255',
            // 'auto_area_id'  => 'required|numeric',
        ]);

        // Get the phone number from validated data
        $phone = $validatedData['phone'];
        $type = $validatedData['type'];

        // If type is 'register', check if the phone number is already registered
        if ($type == 'register') {
            $userExists = User::where('phone_number', $phone)->exists();
            if ($userExists) {
                return ResponseService::error('This phone number is already registered.', [], 400);
            }
            // else {
            //     User::create([
            //         'name'         => $request->name,
            //         'phone_number' => $request->phone,
            //         'auto_area_id' => $request->auto_area_id,
            //     ]);
            // }
        }

        // If type is 'login', check if the phone number exists in the users table
        if ($type == 'login') {
            $userExists = User::where('phone_number', $phone)->exists();
            if (!$userExists) {
                return ResponseService::error('This phone number is not registered.', [], 400);
            }
        }

        // Generate OTP
        $otp = rand(1000, 9999);

        // Prepare SMS message
        $sms = "Dear Customer,$otp is your verification code -PNGOTP";

        // Set API URL and Parameters for Ping4SMS API
        $url = 'http://site.ping4sms.com/api/smsapi';
        $params = [
            'key' => '1dfbed55da0ae9f9cf4ffd1d350abd06',
            'route' => 2,
            'sender' => 'PNGOTP',
            'number' => $phone,
            'sms' => $sms,
            'templateid' => '1507165967974501361'
        ];

        // Create a new Guzzle client instance
        $client = new Client();

        $response = $client->post($url, [
            'form_params' => $params
        ]);

        $statusCode = $response->getStatusCode();  // 200 if successful
        $responseBody = $response->getBody()->getContents();

        // Log the response from the API for debugging
        //Log::info('Ping4SMS API Response: ' . $responseBody);


        // Check if the request was successful
        if ($statusCode === 200) {
            // Check if OTP already exists for the phone number in AutoOtp table
            $existingOtp = AutoOtp::where('phone', $phone)->first();

            if ($existingOtp) {
                // Update OTP if it already exists
                $existingOtp->otp = $otp;
                $existingOtp->status = 'pending';  // Set status to pending, as it's yet to be verified
                $existingOtp->updated_at = now();  // Update the timestamp if needed
                $existingOtp->save();
            } else {
                // Save a new OTP if it does not exist
                $autoOtp = new AutoOtp;
                $autoOtp->phone = $phone;
                $autoOtp->otp = $otp;
                $autoOtp->status = 'pending';  // Set status to pending, as it's yet to be verified
                $autoOtp->save();
            }

            // Return success response
            return ResponseService::success([], "OTP sent successfully.");
        }  else {
            return ResponseService::error('Failed to send OTP. Please try again.');
        }
    }

    public function verifyOtp(Request $request)
    {
        // Validate phone number, OTP, and type (register or login)
        $validatedData = $request->validate([
            'phone' => 'required|integer',
            'otp' => 'required|integer',  // Ensure the OTP is numeric
            'type' => 'required|string|in:register,login',  // Validate type (register or login)
            'name'          => 'required|string|max:255',
            'auto_area_id'  => 'required|numeric',
        ]);
        //playstore checking loing
        if($validatedData['type'] == 'login' && isReviewLogin('driver', $validatedData['phone'], $validatedData['otp'])){

            $user = User::where('phone_number', $validatedData['phone'])->first();

            if ($user) {
                // Authenticate the user
                Auth::login($user);

                return ResponseService::success('Phone number successfully registered and OTP verified.');
            } else {
                // If the user does not exist, return an error
                return ResponseService::error('Sorry something went wrong.', [], 400);
            }
        } else {

            // Get the phone number, OTP, and type from validated data
            $phone = $validatedData['phone'];
            $otp = $validatedData['otp'];
            $type = $validatedData['type']; // 'register' or 'login'

            // Check if there's an existing OTP record for the phone number
            $autoOtp = AutoOtp::where('phone', $phone)
                ->orderBy('created_at', 'desc')
                ->first(); // Get the latest OTP record

            if (!$autoOtp) {
                return ResponseService::error('No OTP found for this phone number.', [], 400);
            }

            // Verify if the OTP matches
            if ($autoOtp->otp != $otp) {
                return ResponseService::error('Invalid OTP. Please try again.', [], 400);
            }

            // If the type is 'register' - Create a new user
            if ($type === 'register') {
                // Check if the phone number is already registered in the users table
                $userExists = User::where('phone_number', $phone)->exists();
                if ($userExists) {
                    return ResponseService::error('This phone number is already registered. Please login instead.', [], 400);
                }

                // Register the user by creating a new record in the users table
                $user = new User;
                $user->phone_number = $phone;
                $user->name = $request->name;
                $user->auto_area_id = $request->auto_area_id;
                $user->otp = $otp;
                Carbon::setLocale('en');
                date_default_timezone_set('Asia/Kolkata');
                $user->created_at = Carbon::now();

                $user->save();

                // Update OTP status to 'verified'
                $autoOtp->status = 'verified';
                $autoOtp->save();

                // $token = $user->createToken('auth_token')->plainTextToken;

                // Return success response for registration
                return ResponseService::success('Phone number successfully registered and OTP verified.');
                // return ResponseService::success([
                //     'user' => $user,
                //     'token' => $token,
                // ],'Phone number successfully registered and OTP verified.');
                // return response()->json([
                //     'status' => 200,
                //     'message' => 'Login successful.',
                //     'user' => $user,
                //     'token' => $token,
                // ], 200);
            }

            // If the type is 'login' - Verify OTP for an existing user
            if ($type === 'login') {
                // Check if the phone number is already registered in the users table
                $user = User::where('phone_number', $phone)->first();

                if (!$user) {
                    return ResponseService::error('This phone number is not registered. Please register first.', [], 400);
                }

                // If the phone number exists, mark OTP as verified
                $autoOtp->status = 'verified';
                $autoOtp->save();

                $user->otp = $otp;  // You can set a default password or ask for one later
                $user->save();

                // Return success response for login
                return ResponseService::success([],'OTP verified successfully, and you are logged in.');
            }
        }
    }

    public function sendOtpNew(Request $request)
    {
        $validatedData = $request->validate([
            'phone' => 'required|integer',
        ]);
        try {
            // Generate OTP
            $otp = rand(1000, 9999);
            // Prepare SMS message
            $sms = "Dear Customer,$otp is your verification code -PNGOTP";

            // Set API URL and Parameters for Ping4SMS API
            $url = 'http://site.ping4sms.com/api/smsapi';
            $params = [
                'key'        => config('services.ping4sms.key'),
                'route'      => 2,
                'sender'     => 'PNGOTP',
                'number'     => $request->phone,
                'sms'        => $sms,
                'templateid' => '1507165967974501361'
            ];

            // Create a new Guzzle client instance
            $client = new Client();

            $response = $client->post($url, [
                'form_params' => $params
            ]);

            $statusCode     = $response->getStatusCode();  // 200 if successful
            $responseBody   = $response->getBody()->getContents();
            // Check if the request was successful
            if ($statusCode === 200) {
                // Check if OTP already exists for the phone number in AutoOtp table
                $existingOtp = AutoOtp::where('phone', $request->phone)->first();

                if ($existingOtp) {
                    // Update OTP if it already exists
                    $existingOtp->otp        = $otp;
                    $existingOtp->status     = 'pending';  // Set status to pending, as it's yet to be verified
                    $existingOtp->updated_at = now();  // Update the timestamp if needed
                    $existingOtp->save();
                } else {
                    // Save a new OTP if it does not exist
                    $autoOtp            = new AutoOtp;
                    $autoOtp->phone     = $request->phone;
                    $autoOtp->otp       = $otp;
                    $autoOtp->status    = 'pending';  // Set status to pending, as it's yet to be verified
                    $autoOtp->save();
                }
                // Return success response
                return ResponseService::success([], "OTP sent successfully.");
            }  else {
                return ResponseService::error('Failed to send OTP. Please try again.');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::error('Validation Error',[$e->errors()]);
        } catch (\Exception $e) {
            return ResponseService::error('Some went wrong',[safeApiMessage($e)]);
        }
    }

    public function verifyOtpNew(Request $request)
    {
        $validatedData = $request->validate([
            'phone'     => 'required|integer',
            'otp'       => 'required|integer',  // Ensure the OTP is numeric
            'fcm_token' => 'required',
            'device_id' => 'required',
        ]);
        if(isReviewLogin('driver', $request->phone, $request->otp)){

            $user = User::where('phone_number', $request->phone)->first();

            if ($user) {
                // Auth::login($user);
                $token = $user->createToken('auth_token')->plainTextToken;
                return ResponseService::success(['user' => $user,'token' => $token],'Login Successfully',200);
                // return ResponseService::success('Phone number successfully registered and OTP verified.');
            } else {
                // If the user does not exist, return an error
                return ResponseService::error('Sorry something went wrong.', [], 400);
            }
        } else {
            // Check if there's an existing OTP record for the phone number
            $autoOtp = AutoOtp::where('phone', $request->phone)
                ->orderBy('created_at', 'desc')
                ->first(); // Get the latest OTP record

            if (!$autoOtp) {
                return ResponseService::error('No OTP found for this phone number.', [], 400);
            }

            // Verify if the OTP matches
            if ($autoOtp->otp != $request->otp) {
                return ResponseService::error('Invalid OTP. Please try again.', [], 400);
            }
            $autoOtp->status = 'verified';
            $autoOtp->save();
            $user = User::where('phone_number',$request->phone)->first();
            if($user){
                $user->last_login   = now();
                $user->device_id    = $request->device_id;
                $user->device_token = $request->fcm_token;
                $user->save();
            } else{
                $user               = new User();
                $user->phone_number = $request->phone;
                $user->device_id    = $request->device_id;
                $user->device_token = $request->fcm_token;
                $user->last_login   = now();
                $user->save();
            }
            $authUser = User::where('phone_number',$request->phone)->first();
            $token = $authUser->createToken('auth_token')->plainTextToken;
            // return ResponseService::success(['user' => $user,'token' => $token],'Login Successfully',200);
            return response()->json([
                'status'    => 200,
                'message'   => 'Login successful.',
                'user'      => $authUser,
                'token'     => $token,
            ], 200);
        }
    }
}
