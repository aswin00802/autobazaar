<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserInformation;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function editProfilePicture(Request $request)
    {
        $request->validate([
            'profile' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $user = Auth::user();
        $userInformation = UserInformation::firstOrNew(['user_id' => $user->id]);

        try {
            if ($request->hasFile('profile') && $request->file('profile')->isValid()) {

                if ($userInformation->profile && file_exists(public_path($userInformation->profile))) {
                    unlink(public_path($userInformation->profile));
                }


                $file = $request->file('profile');
                $fileName = uniqid() . '.' . $file->getClientOriginalExtension();


                $destinationPath = public_path('uploads/profile_pictures');
                $file->move($destinationPath, $fileName);


                $userInformation->profile = 'uploads/profile_pictures/' . $fileName;
                $userInformation->save();

                return ResponseService::success("Profile picture updated successfully.");
            }

            return ResponseService::error('Invalid file or no file uploaded');
        } catch (\Exception $e) {

            return ResponseService::error("An error occurred while updating the profile picture: " . $e->getMessage());
        }
    }
    
    public function getProfile_old(Request $request)
    {
        $userid = Auth::user()->id;

        $user = User::with(['userInfo', 'driverRequest','autoAreas'])->where('id', $userid)->first();

        if ($user) {
            $user = [
                "id"                    => $user->id ?? 0,
                'profile'               => $user->userInfo->profile ?? null,
                'name'                  =>  $user->name ?? null,
                'email'                 => $user->email ?? null,
                'phone_number'          => $user->phone_number ?? null,
                'role_id'               => $user->role_id ?? null,
                'auto_area_id'          => $user->auto_area_id ?? null,
                'auto_area'             => $user->autoAreas?->name ?? null, 
                'driver_request_status' => $user->driverRequest->status ?? null,
            ];
            return ResponseService::success($user, "User data fetched successfully.");
        } else {
            return ResponseService::success($user, "No data available.");
        }
    }

    public function getProfile(Request $request)
    {
        $user = User::with([
            'userInfo.autoBrand',
            'userInfo.autoModel',
            'userInfo.autoFueltype',
            'driverRequest',
            'autoAreas'
        ])->find(Auth::id());

        if (!$user) {
            return ResponseService::success([], "No data available.");
        }

        $data = [
            "id"                    => $user->id,
            "profile"               => $user->userInfo->profile ?? null,
            "name"                  => $user->name,
            "email"                 => $user->email,
            "phone_number"          => $user->phone_number,
            "role_id"               => $user->role_id,
            "auto_area_id"          => $user->auto_area_id,
            "auto_area"             => $user->autoAreas?->name,
            "driver_request_status" => $user->driverRequest->status ?? null,
            "fair_price_enabled"    => $user->fair_price_enabled,

            "user_info" => [
                "dob"                    => $user->userInfo->dob ?? null,
                "gender"                 => $user->userInfo->gender ?? null,
                "blood_group"            => $user->userInfo->blood_group ?? null,

                "brand"                  => $user->userInfo->autoBrand->brand_name ?? null,
                "model"                  => $user->userInfo->autoModel->model_name ?? null,
                "fuel"                   => $user->userInfo->autoFueltype->name ?? null,

                "brand_id"               => $user->userInfo->brand_id ?? null,
                "model_id"               => $user->userInfo->model_id ?? null,
                "fuel_id"                => $user->userInfo->fuel_id ?? null,

                "millage"                => $user->userInfo->millage ?? null,
                "vehicle_no"             => $user->userInfo->vehicle_no ?? null,
                "seating_capacity"       => $user->userInfo->seating_capacity ?? null,

                "driving_license_no"     => $user->userInfo->driving_license_no ?? null,
                "driving_license_expiry" => $user->userInfo->driving_license_expiry ?? null,

                "rc_book_no"             => $user->userInfo->rc_book_no ?? null,

                "insurance_expiry"       => $user->userInfo->insurance_expiry ?? null,

                "permit_no"              => $user->userInfo->permit_no ?? null,
                "permit_expiry"          => $user->userInfo->permit_expiry ?? null,
            ]
        ];

        return ResponseService::success($data, "User data fetched successfully.");
    }

    public function editPersonalInfo(Request $request){
        $validatedData = $request->validate([
            'name'          =>'required|string',
            'auto_area_id'  => 'required|numeric',
            // 'email'=>'required'
        ]);
        $userid = Auth::user()->id;
        $user = User::where('id', $userid)->first();
        if($user){
            $user->update([
                'name'          => $validatedData['name'],
                'auto_area_id'  => $validatedData['auto_area_id'],
                // 'email' => $validatedData['email'],
            ]);
            return ResponseService::success("Personal Informations updated successfully.");

        }else{
            return ResponseService::error('something went wrong');
        }

    }

    public function deviceToken(Request $request)
    {
        try{
            $validatedData = $request->validate([
                'device_token' => 'required|string'
            ]);
            if (Auth::check()) {
                Auth::user()->update([
                    'device_token' => $validatedData['device_token']
                ]);
                return ResponseService::success([],'Device Token Updated Successfully.');
            }
            return ResponseService::error('User not authenticated.');
        }catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        }  catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', ['error' => $e->getMessage()], 500);
        }
    }

    public function fetchLocation(Request $request)
    {
        $validatedData = $request->validate([
            'latitude'          => ['required', 'numeric', 'between:-90,90'],
            'longitude'         => ['required', 'numeric', 'between:-180,180'],
            'current_location'  => ['required', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        if ($user) {
            $user->update([
                'latitude'         => round($validatedData['latitude'], 8),
                'longitude'        => round($validatedData['longitude'], 8),
                'current_location' => $validatedData['current_location'],
            ]);

            return ResponseService::success(
                [
                'latitude'         => number_format($user->latitude, 8, '.', ''),
                'longitude'        => number_format($user->longitude, 8, '.', ''),
                'current_location' => $user->current_location,
                ],
                'Location updated successfully'
            );
        }

        return ResponseService::error('Something went wrong.');
    }

    public function updateAutoProfile(Request $request)
    {
        try{
            $data = $request->validate([
                'brand_id'      => 'required|integer',
                'model_id'      => 'required|integer',
                'fuel_id'       => 'required|integer',
                'vehicle_no'    => 'required|string',
                'millage'       => 'required|integer',
            ]);
            $user = Auth::user();
            if(!$user){
                return ResponseService::error('Sorry, User Not Found!....');
            }
            //user information update
            UserInformation::updateOrCreate(
                ['user_id' => $user->id],
                Arr::only($data, [
                    'brand_id','model_id','fuel_id','vehicle_no','millage'
                ])
            );
            $user->load('userInfo');
            return response()->json([
                'status'  => true,
                'message' => 'Auto meter created successfully',
                'user'    => $user,
            ]);
            
        }catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        } catch(\Exception $e){
            return ResponseService::error("An error occurred: " . $e->getMessage());
        }
    }

    public function updateDriverProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dob'                       => 'nullable|date',
            'gender'                    => 'required|in:male,female,other',
            'blood_group'               => 'nullable|string|max:5',
            'brand_id'                  => 'required|integer',
            'model_id'                  => 'required|integer',
            'fuel_id'                   => 'required|integer',
            'vehicle_no'                => 'required|string|max:30',
            'millage'                   => 'nullable|string|max:30',
            'seating_capacity'          => 'required|integer|min:1|max:10',
            'driving_license_no'        => 'required|string|max:50',
            'driving_license_expiry'    => 'required|date',
            'rc_book_no'                => 'required|string|max:50',
            'insurance_expiry'          => 'required|date',
            'permit_no'                 => 'nullable|string|max:50',
            'permit_expiry'             => 'nullable|date',
            // 'fitness_expiry'            => 'nullable|date',

            // 'driving_license_image'     => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // 'rc_book_image'             => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            // 'insurance_image'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first()
            ],422);
        }

        DB::beginTransaction();
        try{
            $userId = Auth::id();
            $userInformation = UserInformation::firstOrCreate([
                'user_id' => $userId
            ]);
            $data = [
                'dob'                       => $request->dob,
                'gender'                    => $request->gender,
                'blood_group'               => $request->blood_group,
                'brand_id'                  => $request->brand_id,
                'model_id'                  => $request->model_id,
                'fuel_id'                   => $request->fuel_id,
                'vehicle_no'                => strtoupper($request->vehicle_no),
                'millage'                   => $request->millage,
                'seating_capacity'          => $request->seating_capacity,
                'driving_license_no'        => $request->driving_license_no,
                'driving_license_expiry'    => $request->driving_license_expiry,
                'rc_book_no'                => $request->rc_book_no,
                'insurance_expiry'          => $request->insurance_expiry,
                'permit_no'                 => $request->permit_no,
                'permit_expiry'             => $request->permit_expiry,
                // 'fitness_expiry'            => $request->fitness_expiry,
            ];
            // // Driving License Image
            // if($request->hasFile('driving_license_image')){

            //     $data['driving_license_image'] = uploadedAsset(
            //         $request,
            //         'driving_license_image',
            //         'driving_license_'.$userId,
            //         'driver_documents'
            //     );
            // }

            // // RC Book Image
            // if($request->hasFile('rc_book_image')){

            //     $data['rc_book_image'] = uploadedAsset(
            //         $request,
            //         'rc_book_image',
            //         'rc_book_'.$userId,
            //         'driver_documents'
            //     );
            // }

            // // Insurance Image
            // if($request->hasFile('insurance_image')){

            //     $data['insurance_image'] = uploadedAsset(
            //         $request,
            //         'insurance_image',
            //         'insurance_'.$userId,
            //         'driver_documents'
            //     );
            // }
            $userInformation->update($data);
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Driver information updated successfully.',
                'data' => $userInformation->fresh()
            ]);
        } catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ],500);

        }
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $driver = auth()->user();

        $driver->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Location Updated'
        ]);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        $user->selected_mode = null;

        $user->save();

        $user->currentAccessToken()->delete();

        return response()->json([
            'status' => true,
            'message'=> 'Logout Successfully'
        ]);
    }

    public function selectMode(Request $request)
    {
        $request->validate([
            'mode'=>'required|in:jp_auto,fareprice'
        ]);

        $user = auth()->user();

        $user->selected_mode = $request->mode;
        $user->save();

        return response()->json([
            'status'        => true,
            'message'       => 'Mode Selected',
            'selected_mode' => $user->selected_mode
        ]);
    }
}
