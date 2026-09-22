<?php

namespace App\Http\Controllers\Api\fairprice\V1;

use App\Http\Controllers\Controller;
use App\Models\RideRequest;
use App\Models\RideRequestDriver;
use App\Models\User;
use App\Services\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\CommonFirebaseNotification;
use App\Models\CancelReason;
use App\Services\FairPriceFareService;
use App\Services\FairPriceRideDispatchService;
use App\Services\FairPriceRideFlowService;
use App\Services\RideCommunicationService;
use App\Services\RazorpayPaymentService;
use Illuminate\Support\Facades\Schema;


class AutoController extends Controller
{
    private FairPriceFareService $fareService;

    private FairPriceRideDispatchService $dispatchService;

    private RazorpayPaymentService $paymentService;

    private FairPriceRideFlowService $flowService;

    public function __construct(
        FairPriceFareService $fareService,
        FairPriceRideDispatchService $dispatchService,
        RazorpayPaymentService $paymentService,
        FairPriceRideFlowService $flowService
    ) {
        $this->fareService = $fareService;
        $this->dispatchService = $dispatchService;
        $this->paymentService = $paymentService;
        $this->flowService = $flowService;
    }

    public function findAuto_old(Request $request)
    {
        $validatedData = $request->validate([
            'from_address'    => 'required',
            'from_latitude'   => 'required', 
            'from_longitude'  => 'required',
            'to_address'      => 'required',
            'to_latitude'     => 'required', 
            'to_longitude'    => 'required',
        ]);

        $user = auth('customer')->user();

        if(!$user){
            return ResponseService::error('User not found?....', [], 400);
        }

        $drivers = $this->findNearbyDrivers();

        if($drivers->count() > 0){

            $fuels = $drivers->map(function($driver){
                return [
                    'fuel_id'   => optional($driver->userInfo)->fuel_id,
                    'fuel_name' => optional($driver->userInfo->autoFueltype)->name
                ];
            })->unique('fuel_id')->values();

            return response()->json([
                'status' => true,
                'data'   => $fuels
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'No drivers found'
        ]);
    }

    public function findAuto(Request $request)
    {
        $validatedData = $request->validate([
            'from_address'    => 'required',
            'from_latitude'   => 'required', 
            'from_longitude'  => 'required',
            'to_address'      => 'required',
            'to_latitude'     => 'required', 
            'to_longitude'    => 'required',
            'passenger_count' => 'nullable|integer|min:1|max:3',
        ]);

        $user = auth('customer')->user();

        if(!$user){
            return ResponseService::error('User not found?....', [], 400);
        }

        $drivers = $this->findNearbyDrivers();

        if($drivers->count() > 0){

            $passengerCount = $this->fareService->normalizePassengerCount(
                (int) ($validatedData['passenger_count'] ?? 1)
            );
            $distance = $this->fareService->calculateDistance(
                $request->from_latitude,
                $request->from_longitude,
                $request->to_latitude,
                $request->to_longitude
            );
            $settings = $this->fareService->getSettings();
            $tripBreakdown = $this->fareService->calculateTripFare($distance, $settings, $passengerCount);

            $fuels = $drivers->map(function($driver){
                return [
                    'fuel_id'   => optional($driver->userInfo)->fuel_id,
                    'fuel_name' => optional($driver->userInfo->autoFueltype)->name
                ];
            })->unique('fuel_id')->values();

            return response()->json([
                'status'         => true,
                'data'           => $fuels,
                'distance_km'    => $this->num($distance),
                'passenger_count'=> $passengerCount,
                'estimated_fare' => $this->num($tripBreakdown['trip_fare']),
                'payment_method' => 'cash',
                'fare_breakdown' => [
                    'passenger_count' => $passengerCount,
                    'actual_trip_distance_km' => $this->num($tripBreakdown['actual_distance_km']),
                    'base_km' => $this->num($tripBreakdown['base_km']),
                    'base_fare' => $this->num($tripBreakdown['base_fare']),
                    'extra_billable_km' => $this->num($tripBreakdown['extra_distance_km']),
                    'trip_per_km_rate' => $this->num($tripBreakdown['trip_per_km_rate']),
                    'base_fare_applied' => (bool) $tripBreakdown['base_fare_applied'],
                    'trip_fare' => $this->num($tripBreakdown['trip_fare']),
                    'pickup_fare' => 0.0,
                    'waiting_fare' => 0.0,
                    'total_estimated_fare' => $this->num($tripBreakdown['trip_fare']),
                ]
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'No drivers found'
        ]);
    }

    private function findNearbyDrivers_withdistance($latitude, $longitude)
    {
        $radiuses = [1,2,5,10];

        foreach ($radiuses as $radius) {

            $drivers = User::with(['userInfo.autoFueltype'])
                ->select("users.*")
                ->selectRaw("(6371 * acos(cos(radians(?)) 
                    * cos(radians(latitude)) 
                    * cos(radians(longitude) - radians(?)) 
                    + sin(radians(?)) 
                    * sin(radians(latitude)))) AS distance",
                    [$latitude, $longitude, $latitude])
                ->having("distance","<=",$radius)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->nearBox($latitude, $longitude, $radius)
                ->whereHas('userInfo', function($q){
                    $q->whereNotNull('fuel_id');
                })
                ->orderBy("distance")
                ->get();

            if($drivers->count() > 0){
                return $drivers;
            }
        }

        return collect();
    }

    private function findNearbyDrivers()
    {
        $drivers = User::with(['userInfo.autoFueltype'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereHas('userInfo', function($q){
                $q->whereNotNull('fuel_id');
            })
            ->get();

        return $drivers;
    }

    public function nearestAutos(Request $request)
    {
        $validatedData = $request->validate([
            'latitude'   => 'required', 
            'longitude'  => 'required',
        ]);
        
        $user = auth('customer')->user();

        if(!$user){
            return ResponseService::error('User not found?....', [], 400);
        }

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
                ->nearBox($latitude, $longitude, $radius)
                ->where('fair_price_enabled', 1)
                ->where('is_online', 1)
                ->where('is_available', 1)
                ->orderBy("distance")
                ->get();

            if($drivers->count() > 0){

                return response()->json([
                    'status' => true,
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
    }

    private function findDriversByFuel($fuel_id, $lat, $lng)
    {
        $radiuses = [1, 2, 5, 10];

        foreach ($radiuses as $radius) {

            $driversQuery = User::with('userInfo')
                ->select("users.*")
                ->selectRaw("(6371 * acos(cos(radians(?)) 
                    * cos(radians(latitude)) 
                    * cos(radians(longitude) - radians(?)) 
                    + sin(radians(?)) 
                    * sin(radians(latitude)))) AS distance",
                    [$lat, $lng, $lat])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->nearBox($lat, $lng, $radius)
                // ->where('is_online',1)
                // ->where('is_available',1)

                // ->whereHas('userInfo', function($q) use ($fuel_id){
                //     $q->where('fuel_id',$fuel_id);
                // })

                ->having('distance','<=',$radius)
                ->orderBy('distance');

            if (Schema::hasColumn('users', 'fair_price_enabled')) {
                $driversQuery->where('fair_price_enabled', 1);
            }

            $drivers = $driversQuery->get();

            if($drivers->count() > 0){
                return $drivers;
            }
        }

        return collect();

        // only get particular user
        // return User::with('userInfo')
        //         ->select("users.*")
        //         //->where('id', 1846)
        //         //->where('id', 2042)
        //         ->where('id',60)
        //         ->whereNotNull('latitude')
        //         ->whereNotNull('longitude')
        //         // ->whereHas('userInfo', function($q) use ($fuel_id){
        //         //     $q->where('fuel_id', $fuel_id);
        //         // })
        //         ->get();
    }

    public function createRideRequest(Request $request)
    {
        $validated = $request->validate([
            'from_address'  => 'required',
            'from_latitude' => 'required',
            'from_longitude'=> 'required',
            'to_address'    => 'nullable|string',
            'to_latitude'   => 'nullable|numeric',
            'to_longitude'  => 'nullable|numeric',
            'ride_for'          => 'nullable|string',
            'passenger_count'   => 'nullable|integer|min:1|max:3',
            'fuel_id'       => 'nullable',
            'booking_type'  => 'nullable|in:instant,prebook,virtual_stand,hire,tour',
            'scheduled_at'  => 'nullable|date',
            'pickup_at'     => 'nullable|date',
            'drop_at'       => 'nullable|date',
            'other_phone'   => 'nullable|digits:10',
            'driver_id'     => 'nullable|integer',
            'hire_type'     => 'nullable|in:daily,monthly',
            'purpose'       => 'nullable|string|max:50',
            'booking_comment' => 'nullable|string|max:1000',
        ]);

        $rideFor = trim((string) ($validated['ride_for'] ?? 'self')) ?: 'self';
        $passengerCount = $this->fareService->normalizePassengerCount((int) ($validated['passenger_count'] ?? 1));
        $otherPhone = $this->shouldStoreOtherPhone($rideFor, $validated['other_phone'] ?? null);
        $bookingType = $validated['booking_type'] ?? 'instant';
        $customer = auth('customer')->user();

        $activeRide = RideRequest::where('customer_id', $customer->id)
            ->whereIn('status', ['pending', 'accepted', 'arrived', 'started'])
            ->first();

        if ($activeRide) {
            return response()->json([
                'status'         => false,
                'message'        => 'You already have an active ride. Complete or cancel it first.',
                'active_ride_id' => (int) $activeRide->id,
            ], 422);
        }

        $scheduledAt = null;
        $pickupAt = null;
        $dropAt = null;
        $hireType = null;
        $purpose = isset($validated['purpose']) ? strtolower(trim((string) $validated['purpose'])) : null;
        $bookingComment = $validated['booking_comment'] ?? null;
        $isRoundTrip = false;
        $targetDriverId = null;

        // Instant / prebook / virtual_stand still need drop location
        if (!in_array($bookingType, ['tour'], true)) {
            if (empty($validated['to_address']) || empty($validated['to_latitude']) || empty($validated['to_longitude'])) {
                return response()->json([
                    'status' => false,
                    'message' => 'to_address, to_latitude and to_longitude are required',
                ], 422);
            }
        }

        if (in_array($bookingType, ['prebook', 'hire', 'tour'], true)) {
            $pickupAt = !empty($validated['pickup_at'])
                ? Carbon::parse($validated['pickup_at'])
                : (!empty($validated['scheduled_at']) ? Carbon::parse($validated['scheduled_at']) : null);

            if (!$pickupAt) {
                return response()->json([
                    'status' => false,
                    'message' => 'pickup_at is required for ' . $bookingType . ' rides',
                ], 422);
            }

            if ($pickupAt->lt(now()->addHours(3))) {
                return response()->json([
                    'status' => false,
                    'message' => ucfirst($bookingType) . ' rides require minimum 3 hours advance booking',
                ], 422);
            }

            if (in_array($bookingType, ['hire', 'tour'], true)) {
                if (empty($validated['drop_at'])) {
                    return response()->json([
                        'status' => false,
                        'message' => 'drop_at is required for ' . $bookingType . ' rides',
                    ], 422);
                }
                $dropAt = Carbon::parse($validated['drop_at']);
                if ($dropAt->lte($pickupAt)) {
                    return response()->json([
                        'status' => false,
                        'message' => 'drop_at must be after pickup_at',
                    ], 422);
                }
            }

            // Cron / dispatch still uses scheduled_at (= pickup time)
            $scheduledAt = $pickupAt;
        }

        if ($bookingType === 'hire') {
            $hireType = $validated['hire_type'] ?? null;
            if (!in_array($hireType, ['daily', 'monthly'], true)) {
                return response()->json([
                    'status' => false,
                    'message' => 'hire_type must be daily or monthly',
                ], 422);
            }

            $hirePurposes = ['school', 'college', 'office', 'other'];
            if (!in_array($purpose, $hirePurposes, true)) {
                return response()->json([
                    'status' => false,
                    'message' => 'purpose must be school, college, office or other',
                ], 422);
            }

            if ($purpose === 'other' && empty($bookingComment)) {
                return response()->json([
                    'status' => false,
                    'message' => 'booking_comment is required when purpose is other',
                ], 422);
            }

            $isRoundTrip = true;
        }

        if ($bookingType === 'tour') {
            $tourPurposes = ['temple', 'beach', 'hillstation', 'family', 'heritage', 'other'];
            if (empty($purpose) || !in_array($purpose, $tourPurposes, true)) {
                return response()->json([
                    'status' => false,
                    'message' => 'purpose must be temple, beach, hillstation, family, heritage or other',
                ], 422);
            }
            if (empty($bookingComment)) {
                return response()->json([
                    'status' => false,
                    'message' => 'booking_comment is required (places / details to visit)',
                ], 422);
            }
            $isRoundTrip = true;
        }

        if ($bookingType === 'virtual_stand') {
            $targetDriverId = (int) ($validated['driver_id'] ?? 0);
            if ($targetDriverId <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'driver_id is required for virtual_stand booking',
                ], 422);
            }
        }

        $settings = $this->fareService->getSettings();
        $hasDropCoords = !empty($validated['to_latitude']) && !empty($validated['to_longitude']);

        if (in_array($bookingType, ['hire', 'tour'], true)) {
            if ($hasDropCoords) {
                $oneWayKm = $this->fareService->calculateDistance(
                    (float) $validated['from_latitude'],
                    (float) $validated['from_longitude'],
                    (float) $validated['to_latitude'],
                    (float) $validated['to_longitude']
                );
            } else {
                // Tour from-only: use admin tour min km for approximate
                $oneWayKm = (float) ($settings['tour_min_km'] ?? 200);
            }

            $tripBreakdown = $this->fareService->calculateRoundTripFare(
                $oneWayKm,
                $bookingType,
                $hireType,
                $settings
            );
            $tripFare = $tripBreakdown['estimated_fare'];
            $estimatedFare = $tripBreakdown['estimated_fare'];
            // Cash at end for now (online advance endpoints kept for later)
            $advanceAmount = null;
            $tripDistanceKm = $tripBreakdown['round_trip_km'];
            $rideStatus = 'scheduled';
            $paymentStatus = 'not_required';
        } else {
            $oneWayKm = $this->fareService->calculateDistance(
                (float) $validated['from_latitude'],
                (float) $validated['from_longitude'],
                (float) $validated['to_latitude'],
                (float) $validated['to_longitude']
            );
            $tripBreakdown = $this->fareService->calculateTripFare($oneWayKm, $settings, $passengerCount);
            $tripFare = $tripBreakdown['trip_fare'];
            $estimatedFare = $tripBreakdown['trip_fare'];
            $tripDistanceKm = $tripBreakdown['actual_distance_km'];
            $advanceAmount = null;
            $rideStatus = $bookingType === 'prebook' ? 'scheduled' : 'pending';
            $paymentStatus = 'not_required';
        }

        DB::beginTransaction();

        try {
            $ride = RideRequest::create([
                'customer_id' => $customer->id,
                'pickup'      => $validated['from_address'],
                'pickup_lat'  => $validated['from_latitude'],
                'pickup_lng'  => $validated['from_longitude'],
                'drop'        => $validated['to_address'] ?? null,
                'drop_lat'    => $validated['to_latitude'] ?? null,
                'drop_lng'    => $validated['to_longitude'] ?? null,
                'fuel_id'     => $validated['fuel_id'] ?? null,
                'ride_for'          => $rideFor,
                'passenger_count'   => $passengerCount,
                'booking_type'      => $bookingType,
                'hire_type'         => $hireType,
                'purpose'           => $purpose,
                'booking_comment'   => $bookingComment,
                'is_round_trip'     => $isRoundTrip,
                'scheduled_at'      => $scheduledAt,
                'pickup_at'         => $pickupAt,
                'drop_at'           => $dropAt,
                'other_phone'       => $otherPhone,
                'target_driver_id'  => $targetDriverId,
                'trip_distance_km'  => $tripDistanceKm,
                'trip_fare'         => $tripFare,
                'pickup_fare'       => 0,
                'waiting_fare'      => 0,
                'estimated_fare'    => $estimatedFare,
                'advance_amount'    => $advanceAmount,
                'payment_status'    => $paymentStatus,
                'waiting_rate'      => $settings['waiting_per_min_rate'],
                'status'            => $rideStatus,
                'date'              => Carbon::today(),
            ]);

            $driverCount = 0;
            if ($bookingType === 'instant') {
                $driverCount = $this->dispatchService->dispatch($ride, $customer);
            } elseif ($bookingType === 'virtual_stand') {
                $ok = $this->dispatchService->dispatchToDriver($ride, $customer, $targetDriverId);
                if (!$ok) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Selected driver not found',
                    ], 404);
                }
                $driverCount = 1;
            }

            DB::commit();

            $response = [
                'status'  => true,
                'message' => match ($bookingType) {
                    'prebook' => 'Prebook ride created. Pay cash at the end of the trip.',
                    'hire', 'tour' => ucfirst($bookingType) . ' booking created. Pay cash at the end of the trip.',
                    'virtual_stand' => 'Virtual stand ride request sent to selected driver',
                    default => 'Ride request sent',
                },
                'ride_id' => (int) $ride->id,
                'booking_type' => $bookingType,
                'passenger_count' => (int) $ride->passenger_count,
                'estimated_fare' => $this->num($ride->estimated_fare),
                'payment_status' => $ride->payment_status,
                'payment_method' => 'cash',
                'payment_required' => false,
            ];

            if (in_array($bookingType, ['prebook', 'hire', 'tour'], true)) {
                $response['scheduled_at'] = $ride->scheduled_at;
                $response['pickup_at'] = $ride->pickup_at;
                $response['drop_at'] = $ride->drop_at;
            }

            if (in_array($bookingType, ['hire', 'tour'], true)) {
                $response['purpose'] = $purpose;
                $response['booking_comment'] = $bookingComment;
                $response['is_round_trip'] = true;
                $response['fare_breakdown'] = [
                    'one_way_km' => $this->num($tripBreakdown['one_way_km']),
                    'round_trip_km' => $this->num($tripBreakdown['round_trip_km']),
                    'per_km_rate' => $this->num($tripBreakdown['per_km_rate']),
                    'estimated_fare' => $this->num($tripBreakdown['estimated_fare']),
                    'note' => 'Approximate only. Final fare after trip complete. Cash payment.',
                ];
            }

            if (!in_array($bookingType, ['hire', 'tour'], true)) {
                $response['fare_breakdown'] = [
                    'passenger_count' => (int) $ride->passenger_count,
                    'trip_fare' => $this->num($tripFare),
                    'estimated_fare' => $this->num($estimatedFare),
                    'payment_method' => 'cash',
                ];
            }

            if ($bookingType === 'hire') {
                $response['hire_type'] = $hireType;
            }

            if ($bookingType === 'virtual_stand') {
                $response['target_driver_id'] = $targetDriverId;
            }

            $response['driver_count'] = $driverCount;

            if ($bookingType === 'instant' && $driverCount === 0) {
                $response['message'] = 'No nearby drivers available. Please try again later.';
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('ride_create.failed', [
                'customer_id'   => $customer?->id,
                'message'       => safeApiMessage($e),
                'line'          => $e->getLine(),
                'file'          => $e->getFile(),
            ]);

            return response()->json([
                'status' => false,
                'message'=> safeApiMessage($e)
            ]);
        }
    }

    public function virtualStandDrivers(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'ride_id' => 'nullable|integer',
        ]);

        $customer = auth('customer')->user();

        $excludeIds = [];
        if (!empty($validated['ride_id'])) {
            $ride = RideRequest::where('id', $validated['ride_id'])
                ->where('customer_id', $customer->id)
                ->where('booking_type', 'virtual_stand')
                ->first();

            if ($ride) {
                $excludeIds = RideRequestDriver::where('ride_id', $ride->id)
                    ->where('status', 'rejected')
                    ->pluck('driver_id')
                    ->all();
            }
        }

        $drivers = $this->dispatchService->findDriversWithinKm(
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            1,
            $excludeIds,
            $customer->phone
        );

        $data = $drivers->map(function ($driver) {
            return [
                'driver_id' => (int) $driver->id,
                'name' => $driver->name,
                'phone' => $driver->phone_number,
                'vehicle_no' => optional($driver->userInfo)->vehicle_no,
                'distance_km' => $this->num($driver->distance),
                'latitude' => $this->num($driver->latitude),
                'longitude' => $this->num($driver->longitude),
            ];
        })->values();

        return response()->json([
            'status' => true,
            'radius_km' => 1.0,
            'count' => $data->count(),
            'drivers' => $data,
        ]);
    }

    public function virtualStandReassign(Request $request)
    {
        $validated = $request->validate([
            'ride_id' => 'required|integer',
            'driver_id' => 'required|integer',
        ]);

        $customer = auth('customer')->user();

        $ride = RideRequest::where('id', $validated['ride_id'])
            ->where('customer_id', $customer->id)
            ->where('booking_type', 'virtual_stand')
            ->where('status', 'pending')
            ->whereNull('driver_id')
            ->first();

        if (!$ride) {
            return response()->json([
                'status' => false,
                'message' => 'Virtual stand ride not available for reassign',
            ], 404);
        }

        $rejected = RideRequestDriver::where('ride_id', $ride->id)
            ->where('status', 'rejected')
            ->where('driver_id', $validated['driver_id'])
            ->exists();

        if ($rejected) {
            return response()->json([
                'status' => false,
                'message' => 'This driver already rejected. Choose another auto.',
            ], 422);
        }

        $ok = $this->dispatchService->dispatchToDriver($ride, $customer, (int) $validated['driver_id']);
        if (!$ok) {
            return response()->json([
                'status' => false,
                'message' => 'Selected driver not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Ride reassigned to selected driver',
            'ride_id' => (int) $ride->id,
            'target_driver_id' => (int) $validated['driver_id'],
        ]);
    }

    // public function acceptRide(Request $request)
    // {
    //     $request->validate([
    //         'ride_id' => 'required'
    //     ]);

    //     $driver = auth()->user();

    //     DB::beginTransaction();

    //     try {

    //         $ride = RideRequest::where('id',$request->ride_id)
    //             ->lockForUpdate()
    //             ->first();

    //         if(!$ride || $ride->status != 'pending'){
    //             return response()->json([
    //                 'status' => false,
    //                 'message'=> 'Ride already taken'
    //             ]);
    //         }

    //         $ride->update([
    //             'driver_id' => $driver->id,
    //             'status'    => 'accepted'
    //         ]);

    //         // Driver busy
    //         $driver->update([
    //             'is_available'=>0
    //         ]);

    //         $this->notifyOtherDrivers($ride->id, $driver->id);

    //         DB::commit();

    //         return response()->json([
    //             'status'  => true,
    //             'message' => 'Ride accepted'
    //         ]);

    //     } catch (\Exception $e){

    //         DB::rollback();

    //         return response()->json([
    //             'status'  => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    // private function notifyOtherDrivers($ride_id,$accepted_driver_id)
    // {
    //     $drivers = RideRequestDriver::where('ride_id',$ride_id)
    //         ->where('driver_id','!=',$accepted_driver_id)
    //         ->get();

    //     $users = User::whereIn('id',$drivers)
    //         ->whereNotNull('device_token')
    //         ->get(['id','device_token']);

    //     $tokens  = $users->pluck('device_token')->toArray();
    //     $userIds = $users->pluck('id')->toArray();

    //     app(\App\Services\CommonFirebaseNotification::class)
    //         ->sendCommonNotification(
    //             $tokens,
    //             "Ride Closed",
    //             "Ride already accepted",
    //             [
    //                 'type'    => 'ride_closed',
    //                 'ride_id' => $ride_id
    //             ],
    //             $userIds
    //         );
    // }

    public function getRideDetails(Request $request,$rideId)
    {
        $customer = auth('customer')->user();
        
        if(!$customer){
            return response()->json([
                'status'  => false,
                'message' => 'User not found'
            ]);
        }

        $ride = RideRequest::with([
                        'driver:id,name,phone_number,latitude,longitude',
                        'driver.userInfo'
                    ])
                    ->where('id',$rideId)
                    ->where('customer_id',$customer->id)
                    ->whereIn('status', ['scheduled','pending','accepted','arrived','started','completed','cancelled'])
                    ->first();

        if(!$ride){
            return response()->json([
                'status'  => false,
                'message' => 'No driver assigned to this ride'
            ]);
        }

        if ($ride->status === 'cancelled') {
            return response()->json([
                'status' => true,
                'data'   => [
                    'id'             => $ride->id,
                    'status'         => $ride->status,
                    'cancelled_by'   => $ride->cancelled_by,
                    'cancel_reason'  => $ride->cancel_reason,
                    'cancelled_at'   => $ride->cancelled_at,
                    'pickup'         => $ride->pickup,
                    'drop'           => $ride->drop,
                ]
            ]);
        }

        $driverDistance = 0;
        if (
            $ride->driver &&
            !empty($ride->driver->latitude) &&
            !empty($ride->driver->longitude) &&
            !empty($ride->pickup_lat) &&
            !empty($ride->pickup_lng)
        ) {
            $driverDistance = round($this->fareService->calculateDistance(
                $ride->driver->latitude,
                $ride->driver->longitude,
                $ride->pickup_lat,
                $ride->pickup_lng
            ), 2);
        }

        $ride->driver_distance_km = $this->num($driverDistance);

        // Customer-only: show start PIN before ride starts (no SMS).
        if (!in_array($ride->status, ['accepted', 'arrived'], true)) {
            $ride->makeHidden(['start_pin']);
        }
        
        return response()->json([
            'status' => true,
            'data'   => $ride
        ]);
    }

    /**
     * Edit passenger count after driver arrives. Recalculates estimated fare.
     */
    public function updatePassengerCount(Request $request, $rideId)
    {
        $validated = $request->validate([
            'passenger_count' => 'required|integer|min:1|max:3',
        ]);

        $customer = auth('customer')->user();
        $passengerCount = $this->fareService->normalizePassengerCount((int) $validated['passenger_count']);

        $ride = RideRequest::where('id', $rideId)
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['accepted', 'arrived'])
            ->whereIn('booking_type', ['instant', 'prebook', 'virtual_stand'])
            ->first();

        if (!$ride) {
            return response()->json([
                'status' => false,
                'message' => 'Passenger count can be edited only after driver is assigned / arrived.',
            ], 422);
        }

        $settings = $this->fareService->getSettings();
        $distance = $this->fareService->calculateDistance(
            (float) $ride->pickup_lat,
            (float) $ride->pickup_lng,
            (float) $ride->drop_lat,
            (float) $ride->drop_lng
        );
        $tripBreakdown = $this->fareService->calculateTripFare($distance, $settings, $passengerCount);
        $estimatedFare = round(
            (float) $tripBreakdown['trip_fare'] + (float) ($ride->pickup_fare ?? 0) + (float) ($ride->waiting_fare ?? 0),
            2
        );

        $ride->update([
            'passenger_count' => $passengerCount,
            'trip_fare' => $tripBreakdown['trip_fare'],
            'trip_distance_km' => $tripBreakdown['actual_distance_km'],
            'estimated_fare' => $estimatedFare,
        ]);

        $this->flowService->notifyDriver(
            $ride->fresh(),
            'Passenger Updated',
            'Passenger count changed to ' . $passengerCount . '. Fare: ₹' . number_format($estimatedFare, 2),
            'passenger_count_updated',
            [
                'passenger_count' => (string) $passengerCount,
                'estimated_fare' => (string) $estimatedFare,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Passenger count updated',
            'ride_id' => (int) $ride->id,
            'passenger_count' => $passengerCount,
            'estimated_fare' => $this->num($estimatedFare),
            'payment_method' => 'cash',
            'fare_breakdown' => [
                'passenger_count' => $passengerCount,
                'base_km' => $this->num($tripBreakdown['base_km']),
                'base_fare' => $this->num($tripBreakdown['base_fare']),
                'extra_billable_km' => $this->num($tripBreakdown['extra_distance_km']),
                'trip_per_km_rate' => $this->num($tripBreakdown['trip_per_km_rate']),
                'trip_fare' => $this->num($tripBreakdown['trip_fare']),
                'pickup_fare' => $this->num($ride->pickup_fare),
                'waiting_fare' => $this->num($ride->waiting_fare),
                'total_estimated_fare' => $this->num($estimatedFare),
            ],
        ]);
    }

    public function getCustomerRideHistory(Request $request)
    {
        $customer = auth('customer')->user();
        if(!$customer){
            return response()->json([
                'status'  => false,
                'message' => 'Customer not found'
            ]);
        }

        $rides = RideRequest::with([
            'driver:id,name,phone_number',
            'driver.userInfo'
        ])
        ->select(
            'id',
            'driver_id',
            'pickup',
            'drop',
            'status',
            'booking_type',
            'hire_type',
            'scheduled_at',
            'payment_status',
            'advance_amount',
            'created_at'
        )
        ->where('customer_id', $customer->id)
        ->whereIn('status', ['scheduled', 'pending', 'accepted', 'arrived', 'started', 'completed', 'cancelled'])
        ->orderByDesc('id')
        ->get();

        $data = $rides->map(function ($ride) {
            $item = [
                'id'              => (int) $ride->id,
                'pickup'          => $ride->pickup,
                'drop'            => $ride->drop,
                'status'          => $ride->status,
                'booking_type'    => $ride->booking_type,
                'payment_status'  => $ride->payment_status,
                'advance_amount'  => $this->num($ride->advance_amount),
                'created_at'      => optional($ride->created_at)->toIso8601String(),
                'scheduled_at'    => optional($ride->scheduled_at)->toIso8601String(),
                'driver'          => [
                    'name'         => $ride->driver->name ?? null,
                    'phone_number' => $ride->driver->phone_number ?? null,
                    'vehicle_no'   => $ride->driver->userInfo->vehicle_no ?? null,
                ],
            ];

            if ($ride->booking_type === 'hire') {
                $item['hire_type'] = $ride->hire_type;
            }

            return $item;
        });

        return response()->json([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function cancelRide(Request $request, $rideId)
    {
        $customer = auth('customer')->user();

        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Customer not found'
            ]);
        }

        $request->validate([
            'reason'        => 'required|string',
            'other_reason'  => 'nullable|string|max:255'
        ]);

        $ride = RideRequest::where('id', $rideId)
                        ->where('customer_id', $customer->id)
                        ->whereIn('status', ['pending', 'accepted'])
                        ->first();

        if (!$ride) {
            return response()->json([
                'status'  => false,
                'message' => 'Ride cannot be cancelled.'
            ]);
        }

        $reason = $request->reason;

        if ($reason == 'Other') {
            $reason = $request->other_reason;
        }

        $driverId = $ride->driver_id;

        $ride->update([
            'status'        => 'cancelled',
            'cancelled_at'  => now(),
            'cancelled_by'  => 'customer',
            'cancel_reason' => $reason
        ]);

        if (!empty($driverId)) {
            User::where('id', $driverId)->update(['is_available' => 1]);
        }

        return response()->json([
            'status'    => true,
            'message'   => 'Ride cancelled successfully.'
        ]);
    }

    public function customerCloseReason()
    {
        $reasons = CancelReason::where('type', 'C')->pluck('name'); // only names as array
        if ($reasons->isEmpty()) {
            return response()->json([
                'status'  => false,
                'message' => 'No reasons found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'data'   => $reasons
        ]);
    }

    public function customerCancelRide(Request $request)
    {
        $request->validate([
            'ride_id' => 'required',
            'reason'  => 'required',
            'other_reason'  => 'nullable|string|max:255'
        ]);

        $customer = auth('customer')->user();

        DB::beginTransaction();

        try {

            $ride = RideRequest::where('id', $request->ride_id)
                ->where('customer_id', $customer->id)
                ->whereIn('status', ['scheduled','pending','accepted'])
                ->lockForUpdate()
                ->first();

            if (!$ride) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Ride cannot be cancelled'
                ]);
            }

            $reason = $request->reason;

            if ($reason == 'Other') {
                $reason = $request->other_reason;
            }

            $this->flowService->autoStopWaiting($ride);

            $ride->update([
                'status'        => 'cancelled',
                'cancelled_by'  => 'customer',
                'cancel_reason' => $reason,
                'cancelled_at'  => now()
            ]);
            app(RideCommunicationService::class)->closeConversation($ride->fresh());

            if (!empty($ride->driver_id)) {
                User::where('id', $ride->driver_id)->update(['is_available' => 1]);
            }

            $refunded = false;
            $refundAmount = null;
            $refundMessage = null;
            $deductionPercent = null;

            if (
                $ride->payment_status === 'paid' &&
                empty($ride->driver_id)
            ) {
                $advancePaid = (float) ($ride->advance_amount ?? $ride->estimated_fare ?? 0);

                if ($ride->booking_type === 'prebook') {
                    $refundAmount = $advancePaid;
                    $deductionPercent = 0;
                    $refunded = $this->paymentService->refundRidePayment($ride->fresh(), $refundAmount);
                } elseif (in_array($ride->booking_type, ['hire', 'tour'], true)) {
                    $refundCalc = $this->fareService->calculateHireTourRefund($advancePaid, $ride->scheduled_at);
                    $deductionPercent = $refundCalc['deduction_percent'];
                    $refundAmount = $refundCalc['refund_amount'];

                    $ride->update([
                        'refund_deduction_percent' => $deductionPercent,
                        'refund_amount' => $refundAmount,
                    ]);

                    if ($refundCalc['refundable']) {
                        $refunded = $this->paymentService->refundRidePayment($ride->fresh(), $refundAmount);
                    } else {
                        $refunded = false;
                        $refundMessage = 'No refund. Cancellation is within 1 day of scheduled time.';
                    }
                }

                if ($refunded) {
                    $refundMessage = 'Refund of ₹' . number_format($refundAmount, 2)
                        . ' has been initiated'
                        . ($deductionPercent ? ' (deduction ' . $deductionPercent . '%)' : '')
                        . '. Amount will reflect in your account within 48 hours to 7 days.';

                    $this->flowService->notifyCustomer(
                        $ride,
                        'Refund Initiated 💰',
                        $refundMessage,
                        'refund_initiated',
                        [
                            'refund_amount' => (string) $refundAmount,
                            'deduction_percent' => (string) ($deductionPercent ?? 0),
                            'refund_status' => 'initiated',
                        ]
                    );
                }
            }

            $this->flowService->notifyCustomer(
                $ride,
                'Ride Cancelled 🚫',
                'Your ride has been cancelled.',
                'ride_cancelled',
                [
                    'cancelled_by' => 'customer',
                    'reason' => (string) ($request->reason ?? ''),
                ]
            );

            //notification cancel
            $drivers = User::where('id', $ride->driver_id)
                        ->whereNotNull('device_token')
                        ->get(['id','device_token']);
            $tokens  = $drivers->pluck('device_token')->toArray();
            $userIds = $drivers->pluck('id')->toArray();

            $title = 'Ride Cancelled 🚫';

            $body = $customer->name . ' cancelled the ride from ' 
                    . $ride->pickup . ' to ' . $ride->drop;

            $dataPayload = [
                'type'               => 'ride_cancelled',
                'ride_id'            => $ride->id,
                'cancelled_by'       => 'customer',
                'reason'             => $request->reason,
                'android_channel_id' => 'normal_v5',
            ];
            $firebase = new CommonFirebaseNotification();

            $firebase->sendCommonNotification(
                $tokens,
                $title,
                $body,
                ['payload' => json_encode($dataPayload)],
                $userIds
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message'=> 'Ride cancelled by customer',
                'ride_id' => (int) $ride->id,
                'refunded' => $refunded,
                'refund_amount' => $this->num($refundAmount),
                'deduction_percent' => $deductionPercent === null ? null : (int) $deductionPercent,
                'refund_message' => $refundMessage,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message'=> 'Error',
                'error'  => safeApiMessage($e)
            ]);
        }
    }

    private function shouldStoreOtherPhone(?string $rideFor, ?string $otherPhone): ?string
    {
        if (empty($otherPhone) || !$this->isOtherRideFor($rideFor)) {
            return null;
        }

        return $otherPhone;
    }

    private function isOtherRideFor(?string $rideFor): bool
    {
        if (empty($rideFor)) {
            return false;
        }

        return str_contains(strtolower($rideFor), 'other');
    }

    private function num($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return round((float) $value, 2);
    }

}
