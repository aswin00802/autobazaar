<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RideRequest;
use App\Models\SosAlert;
use App\Models\User;
use App\Services\CommonFirebaseNotification;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class FarePriceController extends Controller
{
    public function driverFarePriceOnOff(Request $request)
    {
        $request->validate([
            'is_online' => 'required|boolean'
        ]);
        $user = Auth::user();
        if(!$user){
            return ResponseService::error('User not found?....', [], 400);
        }
        $user->is_online = $request->is_online;
        $user->save();
        return response()->json([
            'status'    => true,
            'message'   => 'Driver status updated',
            'user'      => $user,
        ]);
    }

    public function sendSOS(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'latitude'    => 'required|numeric|between:-90,90',
                'longitude'   => 'required|numeric|between:-180,180',
                'ride_id'     => 'required|integer|exists:ride_requests,id',
                'description' => 'nullable|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status'  => false,
                'message' => collect($e->errors())->flatten()->first(),
                'errors'  => $e->errors(),
            ], 422);
        }

        $driver = Auth::user();
        if (!$driver) {
            return ResponseService::error('User not found?....', [], 400);
        }

        $ride = RideRequest::where('id', $validatedData['ride_id'])
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'arrived', 'started'])
            ->first();

        if (!$ride) {
            return response()->json([
                'status'  => false,
                'message' => 'Active ride not found for this driver.',
            ], 404);
        }

        $existingSos = SosAlert::where('ride_id', $ride->id)
            ->where('triggered_by', 'driver')
            ->first();

        if ($existingSos) {
            return response()->json([
                'status'                 => true,
                'message'                => 'SOS already sent for this ride',
                'sos_id'                 => $existingSos->id,
                'already_sent'           => true,
                'admin_notified'         => false,
                'drivers_notified_count' => 0,
            ]);
        }

        $sos = SosAlert::create([
            'ride_id'         => $ride->id,
            'triggered_by'    => 'driver',
            'triggered_by_id' => $driver->id,
            'latitude'        => $validatedData['latitude'],
            'longitude'       => $validatedData['longitude'],
            'description'     => $validatedData['description'] ?? null,
        ]);

        $adminNotified = false;
        $driversNotifiedCount = 0;
        $firebase = new CommonFirebaseNotification();

        try {
            $adminUser = User::where('id', 1846)->whereNotNull('device_token')->first();
            if ($adminUser) {
                $firebase->sendCommonNotification(
                    [$adminUser->device_token],
                    'Fairprice Emergency SOS',
                    ($driver->name ?: 'Driver') . ' triggered emergency SOS alert.',
                    [
                        'payload' => json_encode([
                            'type'               => 'sos_alert',
                            'triggered_by'       => 'driver',
                            'ride_id'            => $ride->id,
                            'sos_id'             => $sos->id,
                            'url'                => 'fareprice-view-sos-alarm/' . $sos->id,
                            'android_channel_id' => 'emergency_alert',
                        ]),
                    ],
                    [$adminUser->id]
                );
                $adminNotified = true;
            }
        } catch (\Throwable $e) {
            Log::error('FairPrice driver SOS admin notification failed.', [
                'sos_id' => $sos->id,
                'error' => safeApiMessage($e),
            ]);
        }

        try {
            $nearbyDrivers = $this->findNearbyOnlineDrivers(
                $validatedData['latitude'],
                $validatedData['longitude'],
                $driver->id
            );
            $tokens = $nearbyDrivers->pluck('device_token')->filter()->values()->toArray();
            $userIds = $nearbyDrivers->pluck('id')->values()->toArray();

            if (!empty($tokens)) {
                $firebase->sendCommonNotification(
                    $tokens,
                    'Fairprice Emergency SOS',
                    ($driver->name ?: 'Driver') . ' needs help nearby. Open SOS details.',
                    [
                        'payload' => json_encode([
                            'type'               => 'driver_sos_alert',
                            'triggered_by'       => 'driver',
                            'ride_id'            => $ride->id,
                            'sos_id'             => $sos->id,
                            'url'                => 'fareprice-view-sos-alarm/' . $sos->id,
                            'android_channel_id' => 'emergency_alert',
                        ]),
                    ],
                    $userIds
                );
                $driversNotifiedCount = count($userIds);
            }
        } catch (\Throwable $e) {
            Log::error('FairPrice driver SOS nearby drivers notification failed.', [
                'sos_id' => $sos->id,
                'error' => safeApiMessage($e),
            ]);
        }

        return response()->json([
            'status'                 => true,
            'message'                => 'SOS alert sent successfully',
            'sos_id'                 => $sos->id,
            'already_sent'           => false,
            'admin_notified'         => $adminNotified,
            'drivers_notified_count' => $driversNotifiedCount,
        ]);
    }

    private function findNearbyOnlineDrivers($latitude, $longitude, $excludeDriverId)
    {
        $radiuses = [1, 2, 5, 10];

        foreach ($radiuses as $radius) {
            $drivers = User::select('users.*')
                ->selectRaw("(6371 * acos(cos(radians(?))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?))
                    * sin(radians(latitude)))) AS distance",
                    [$latitude, $longitude, $latitude])
                ->where('id', '!=', $excludeDriverId)
                ->where('is_online', 1)
                ->where('fare_price_enabled', 1)
                ->whereNotNull('device_token')
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->having('distance', '<=', $radius)
                ->orderBy('distance')
                ->get();

            if ($drivers->isNotEmpty()) {
                return $drivers;
            }
        }

        return collect();
    }

    public function viewSOS($id)
    {
        $sos = SosAlert::with([
            'ride.customer',
            'ride.driver:id,name,phone_number',
            'ride.driver.userInfo'
        ])->findOrFail($id);
        $lat = $sos->latitude;
        $lng = $sos->longitude;
        $address = null;
        if ($lat && $lng) {
            $apiKey = 'AIzaSyAqwWQswTjjFTFXt9AXMlVNtWRtkhGbxfQ';

            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$lng}&key={$apiKey}";

            $response = file_get_contents($url);
            $data = json_decode($response, true);

            if (!empty($data['results'])) {
                $address = $data['results'][0]['formatted_address'];
            }
        }

        $sos->address = $address;
        return response()->json([
            'status' => true,
            'data'   => $sos
        ]);
    }
}
