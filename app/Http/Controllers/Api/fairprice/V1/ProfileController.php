<?php

namespace App\Http\Controllers\Api\fairprice\V1;

use App\Http\Controllers\Controller;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    public function profileUpdate(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name'   => 'required',
                'gender' => 'required', 
                'email'  => [
                    'nullable',
                    'email',
                    Rule::unique('customers', 'email')->ignore(auth('customer')->id()),
                ],
                'dob'    => 'nullable',
                'family_details'                => 'nullable|array|min:2|max:3',
                'family_details.*.name'         => 'required|string|max:100',
                'family_details.*.mobile'       => 'required|digits:10|distinct',
                'family_details.*.relationship' => 'required|string|max:50',
            ], [
                'family_details.array'                   => 'Family details must be a list of members.',
                'family_details.min'                     => 'Please provide at least 2 family members.',
                'family_details.max'                     => 'You can provide a maximum of 3 family members.',
                'family_details.*.name.required'         => 'Family member name is required.',
                'family_details.*.mobile.required'       => 'Family member mobile number is required.',
                'family_details.*.mobile.digits'         => 'Family member mobile number must be 10 digits.',
                'family_details.*.mobile.distinct'       => 'Family member mobile numbers must be different.',
                'family_details.*.relationship.required' => 'Family member relationship is required.',
            ]);
            $user = auth('customer')->user();
            if(!$user){
                return ResponseService::error('User not found?....', [], 400);
            }
            $user->name   = $validatedData['name'];
            $user->gender = $validatedData['gender'];
            $user->email  = $validatedData['email'] ?? null;
            $user->dob    = $validatedData['dob'] ?? null;
            if (array_key_exists('family_details', $validatedData)) {
                $user->family_details = $validatedData['family_details'];
            }
            $user->save();
            return ResponseService::success(['user' => $user], 'Profile updated successfully', 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseService::validationError('Validation failed.', $e->errors(), 422);
        } catch (\Exception $e) {
            return ResponseService::error('An error occurred. Please try again.', [], 500);
        }
    }

    public function getProfile(Request $request)
    {
        $user = auth('customer')->user();
        if(!$user){
            return ResponseService::error('User not found?....', [], 400);
        }
        return ResponseService::success(['user' => $user], 'Profile fetched successfully', 200);
    }

    public function updateLocation(Request $request)
    {
        $validatedData = $request->validate([
            'latitude'          => 'required',
            'longitude'         => 'required', 
            'current_location'  => 'required',
        ]);
        $user = auth('customer')->user();
        if(!$user){
            return ResponseService::error('User not found?....', [], 400);
        }
        $user->latitude   = $validatedData['latitude'] ?? null;
        $user->longitude = $validatedData['longitude'] ?? null;
        $user->current_location  = $validatedData['current_location'] ?? null;
        $user->save();

        $latitude  = $request->latitude ;
        $longitude = $request->longitude ;

        $radiuses = [1,2,5,10];

        foreach ($radiuses as $radius) {

            $drivers = User::select('latitude','longitude')
                ->selectRaw("(6371 * acos(cos(radians(?)) 
                    * cos(radians(latitude)) 
                    * cos(radians(longitude) - radians(?)) 
                    + sin(radians(?)) 
                    * sin(radians(latitude)))) AS distance",
                    [$latitude, $longitude, $latitude])
                ->having("distance","<=",$radius)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy("distance")
                ->get();

            if($drivers->count() > 0){

                return response()->json([
                    'status' => true,
                    'user' => $user,
                    'data' => $drivers->map(function($driver){
                        return [
                            'latitude'  => $driver->latitude,
                            'longitude' => $driver->longitude
                        ];
                    })
                ]);
            }
        }

        return response()->json([
            'status'  => false,
            'message' => 'No drivers found',
            'data'    => []
        ]);

        // return ResponseService::success(['user' => $user],200);
    }

}
