<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'login'     => 'required|string',
    //         'password'  => 'required|string',
    //         // 'fcm_token' => 'required',
    //         // 'device_id' => 'required',
    //     ]);

    //     // Check if login input is email or username
    //     $login_type = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
    //     $credentials = [
    //         $login_type => $request->login,
    //         'password'  => $request->password
    //     ];
        
    //     if (Auth::attempt($credentials)) {
    //         $user  = Auth::user();
    //         // ✅ allow login only if status = 1

    //         if ($user->status_id != 1) {
    //             Auth::logout();

    //             return response()->json([
    //                 'status'  => false,
    //                 'message' => 'Your account is inactive. Please contact support.',
    //             ], 403);
    //         }

    //         $user->update([
    //             // 'fcm_token' => $request->fcm_token,
    //             // 'device_id' => $request->device_id,
    //             'last_login'=> now(),
    //         ]);

    //         $token = $user->createToken('auth_token')->plainTextToken;

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Login successful',
    //             'token'   => $token,
    //             'user'    => $user,
    //         ],200);
    //     }

    //     return response()->json([
    //         'status'  => false,
    //         'message' => 'Invalid credentials',
    //     ], 401);
    // }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone_number' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->errors(),
                ], 422);
            }
            //if check user exit or not
            $user = User::where('phone_number',$request->phone_number)->first();
            if (!$user) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Please sign up. Your phone number is not registered.',
                ], 404);
            }

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => 200,
                'message' => 'Login successful.',
                'user' => $user,
                'token' => $token,
            ], 200);
        } catch (QueryException $e) {

            return response()->json([
                'status' => 500,
                'message' => 'A database error occurred. Please try again later.',
                'error_details' => safeApiMessage($e),
            ], 500);
        } catch (\Exception $e) {

            return response()->json([
                'status' => 500,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error_details' => safeApiMessage($e),
            ], 500);
        }
    }
}
