<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\ResponseService;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'nullable|string|max:255',
                'phone_number' => 'required|numeric',
                'auto_area_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return ResponseService::validationError('Validation failed.', $validator->errors()->toArray());
            }
            // Get the validated data
            $validatedData = $validator->validated(); // This gives you the validated data as an array

            $user= User::where('phone_number', $validatedData['phone_number'])->first();
            if(!$user){
                return ResponseService::error('Phone Number is not registered., Please register this phone number.');
            }else{
                $user->name = $validatedData['name'];
                $user->auto_area_id = $validatedData['auto_area_id']; 
                $user->save(); 
                return ResponseService::success([],'Your Account Registered Successfully.');
            }
        } catch (QueryException $e) {

            return response()->json([
                'status' => 404,
                'message' => 'Database error occurred. Please try again later.',
                'error_details' => safeApiMessage($e),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 404,
                'message' => 'An unexpected error occurred. Please try again later.',
                'error_details' => safeApiMessage($e),
            ], 500);
        }
    }
}
