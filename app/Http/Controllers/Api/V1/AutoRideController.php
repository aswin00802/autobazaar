<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CancelReason;
use App\Models\Customer;
use App\Models\RideRequest;
use App\Models\RideRequestDriver;
use App\Models\User;
use App\Services\CommonFirebaseNotification;
use App\Services\FarePriceFirebaseNotification;
use App\Services\FairPriceFareService;
use App\Services\FairPriceRideFlowService;
use App\Services\RideCommunicationService;
use App\Services\RazorpayPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoRideController extends Controller
{
    private FairPriceFareService $fareService;
    private RazorpayPaymentService $paymentService;
    private FairPriceRideFlowService $flowService;

    public function __construct(
        FairPriceFareService $fareService,
        RazorpayPaymentService $paymentService,
        FairPriceRideFlowService $flowService
    ) {
        $this->fareService = $fareService;
        $this->paymentService = $paymentService;
        $this->flowService = $flowService;
    }

    public function acceptRide(Request $request)
    {
        $request->validate([
            'ride_id' => 'required'
        ]);

        $driver = auth()->user();

        $hasActiveRide = RideRequest::where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'arrived', 'started'])
            ->exists();

        if ($hasActiveRide) {
            return response()->json([
                'status'  => false,
                'message' => 'You already have an active ride. Complete or cancel it first.',
            ], 422);
        }

        DB::beginTransaction();

        try {

            $ride = RideRequest::where('id',$request->ride_id)
                ->lockForUpdate()
                ->first();

            Log::info('ride_accept.ride_loaded', [
                'ride_id'             => $request->ride_id,
                'driver_id_attempt'   => $driver?->id,
                'ride_exists'         => (bool) $ride,
                'ride_status'         => $ride?->status,
                'current_driver_id'   => $ride?->driver_id,
            ]);

            if(!$ride || $ride->status != 'pending'){
                Log::warning('ride_accept.rejected', [
                    'ride_id'             => $request->ride_id,
                    'driver_id_attempt'   => $driver?->id,
                    'reason'              => 'ride_missing_or_not_pending',
                    'ride_status'         => $ride?->status,
                    'current_driver_id'   => $ride?->driver_id,
                ]);
                return response()->json([
                    'status' => false,
                    'message'=> 'Ride already taken'
                ]);
            }

            $ride->loadMissing('customer:id,phone');
            if (
                !empty($ride->customer?->phone) &&
                (string) $ride->customer->phone === (string) $driver->phone_number
            ) {
                return response()->json([
                    'status' => false,
                    'message'=> 'You cannot accept your own booking',
                ], 403);
            }

            $isDriverEligible = RideRequestDriver::where('ride_id', $request->ride_id)
                ->where('driver_id', $driver->id)
                ->where('status', 'pending')
                ->exists();

            if (!$isDriverEligible) {
                Log::warning('ride_accept.rejected', [
                    'ride_id'             => $request->ride_id,
                    'driver_id_attempt'   => $driver?->id,
                    'reason'              => 'driver_not_eligible_for_ride',
                ]);
                return response()->json([
                    'status' => false,
                    'message'=> 'Unauthorized ride access'
                ], 403);
            }

            if (
                $ride->booking_type === 'virtual_stand' &&
                (int) $ride->target_driver_id !== (int) $driver->id
            ) {
                return response()->json([
                    'status' => false,
                    'message'=> 'This ride is assigned to another driver',
                ], 403);
            }

            RideRequestDriver::where('ride_id', $ride->id)
                ->where('driver_id', $driver->id)
                ->update(['status' => 'accepted']);

            $startPin = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            $ride->update([
                'driver_id' => $driver->id,
                'status'    => 'accepted',
                'accepted_at' => now(),
                'start_pin' => $startPin,
                'start_pin_attempts' => 0,
                'start_pin_verified_at' => null,
            ]);
            app(RideCommunicationService::class)->syncConversationForRide($ride->fresh());

            $settings = $this->fareService->getSettings();
            $pickupDistance = 0;
            if (
                !empty($driver->latitude) &&
                !empty($driver->longitude) &&
                !empty($ride->pickup_lat) &&
                !empty($ride->pickup_lng)
            ) {
                $pickupDistance = $this->fareService->calculateDistance(
                    (float) $driver->latitude,
                    (float) $driver->longitude,
                    (float) $ride->pickup_lat,
                    (float) $ride->pickup_lng
                );
            }
            $pickupFareDetails = $this->fareService->calculatePickupFare($pickupDistance, $settings);
            $ride->update([
                'pickup_distance_km' => $pickupFareDetails['actual_distance_km'],
                'pickup_fare' => $pickupFareDetails['pickup_fare'],
                'estimated_fare' => round(((float) $ride->trip_fare) + $pickupFareDetails['pickup_fare'] + ((float) $ride->waiting_fare), 2),
            ]);

            Log::info('ride_accept.updated', [
                'ride_id'    => $ride->id,
                'driver_id'  => $driver->id,
                'status'     => 'accepted',
            ]);

            // Driver busy
            $driver->update([
                'is_available'=>0
            ]);

            $customer = Customer::find($ride->customer_id);
            if ($customer && $customer->device_token) {
                $driverDetails = [
                    'driver_id'   => (int) $driver->id,
                    'driver_name' => $driver->name,
                    'driver_phone'=> $driver->phone_number ?? '',
                    'vehicle_no'  => $driver->userInfo->vehicle_no ?? '',
                ];
                $title = 'Driver Assigned 🚖';
                $body  = $driver->name . ' has accepted your ride';
                $dataPayload = [
                    'type'        => 'ride_accepted',
                    'ride_id'     => (string) $ride->id,
                    'start_pin'   => (string) $startPin,
                    'driver_data' => $driverDetails,
                    'android_channel_id' => 'customer_channel',
                ];
                $firebase = new FarePriceFirebaseNotification();
                $firebase->sendCommonNotification(
                    [$customer->device_token],
                    $title,
                    $body,
                    ['payload' => json_encode($dataPayload)],
                    [$customer->id]
                );
            }

            $this->notifyOtherDrivers($ride->id, $driver->id);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Ride accepted',
                'ride_id' => (int) $ride->id,
                'pin_required' => true,
            ]);

        } catch (\Exception $e){

            DB::rollback();

            Log::error('ride_accept.failed', [
                'ride_id'           => $request->ride_id,
                'driver_id_attempt' => $driver?->id,
                'message'           => safeApiMessage($e),
                'line'              => $e->getLine(),
                'file'              => $e->getFile(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => safeApiMessage($e)
            ]);
        }
    }

    public function rejectRide(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|integer',
            'reason' => 'nullable|string|max:255',
        ]);

        $driver = auth()->user();
        if (!$driver) {
            return response()->json(['status' => false, 'message' => 'Driver not found']);
        }

        DB::beginTransaction();
        try {
            $ride = RideRequest::where('id', $request->ride_id)
                ->lockForUpdate()
                ->first();

            if (
                !$ride ||
                $ride->booking_type !== 'virtual_stand' ||
                $ride->status !== 'pending' ||
                !empty($ride->driver_id)
            ) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Ride cannot be rejected',
                ], 422);
            }

            if ((int) $ride->target_driver_id !== (int) $driver->id) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized ride access',
                ], 403);
            }

            RideRequestDriver::where('ride_id', $ride->id)
                ->where('driver_id', $driver->id)
                ->update(['status' => 'rejected']);

            $ride->update([
                'target_driver_id' => null,
            ]);

            $this->flowService->notifyCustomer(
                $ride,
                'Driver Unavailable',
                'Selected auto rejected your request. Please choose another auto from Virtual Stand.',
                'virtual_stand_driver_rejected',
                [
                    'rejected_driver_id' => (string) $driver->id,
                    'reason' => (string) ($request->reason ?? ''),
                ]
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Ride rejected. Customer can choose another auto.',
                'ride_id' => $ride->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => safeApiMessage($e),
            ], 500);
        }
    }

    private function notifyOtherDrivers($ride_id,$accepted_driver_id)
    {
        $drivers = RideRequestDriver::where('ride_id',$ride_id)
            ->where('driver_id','!=',$accepted_driver_id)
            ->get();

        $driverIds = $drivers->pluck('driver_id')->toArray();

        if (empty($driverIds)) {
            return;
        }

        $users = User::whereIn('id', $driverIds)
            ->whereNotNull('device_token')
            ->get(['id','device_token']);

        // $tokens  = $users->pluck('device_token')->toArray();
        // $userIds = $users->pluck('id')->toArray();

        // app(\App\Services\CommonFirebaseNotification::class)
        //     ->sendCommonNotification(
        //         $tokens,
        //         "Ride Closed",
        //         "Ride already accepted",
        //         [
        //             'type'    => 'ride_closed',
        //             'ride_id' => $ride_id
        //         ],
        //         $userIds
        //     );

        $tokens  = $users->pluck('device_token')->toArray();
        $userIds = $users->pluck('id')->toArray();

        $title    = 'Ride Closed';
        $body = 'Intha ride already oru driver accept pannitanga. Next ride varum wait pannunga.';
        $dataPayload = [
            'type'               => 'ride_closed',
            'android_channel_id' => 'normal_v5',
        ];
        $firebase = new CommonFirebaseNotification();
        $firebase->sendCommonNotification($tokens, $title, $body, ['payload' => json_encode($dataPayload)], $userIds);
    }

    public function getRideDetails(Request $request,$rideId)
    {
        // $request->validate([
        //     'ride_id' => 'required'
        // ]);
        // $ride = RideRequest::with('customer:id,name,phone')
        //             ->where('id',$request->ride_id)
        //             ->where('status','pending')
        //             ->first()
        $driver = auth()->user();

        Log::info('driver_ride_details.request_received', [
            'ride_id'    => $rideId,
            'driver_id'  => $driver?->id,
        ]);

        if(!$driver){
            return response()->json([
                'status'  => false,
                'message' => 'User not found'
            ]);
        }

        $isDriverEligible = RideRequestDriver::where('ride_id', $rideId)
            ->where('driver_id', $driver->id)
            ->exists();

        $ride = RideRequest::with('customer:id,name,phone')
            ->where('id', $rideId)
            ->where(function ($query) use ($driver, $isDriverEligible) {
                if ($isDriverEligible) {
                    $query->where('status', 'pending');
                }

                $query->orWhere(function ($innerQuery) use ($driver) {
                    $innerQuery->where('driver_id', $driver->id)
                        ->whereIn('status', ['accepted', 'arrived', 'started', 'cancelled']);
                });
            })
            ->first();

        if(!$ride){
            $rideSnapshot = RideRequest::select('id', 'status', 'driver_id', 'customer_id')
                ->where('id', $rideId)
                ->first();

            Log::warning('driver_ride_details.not_available', [
                'ride_id'             => $rideId,
                'driver_id'           => $driver->id,
                'expected_status'     => 'pending',
                'snapshot_status'     => $rideSnapshot?->status,
                'snapshot_driver_id'  => $rideSnapshot?->driver_id,
                'snapshot_customer_id'=> $rideSnapshot?->customer_id,
                'is_driver_eligible'  => $isDriverEligible,
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Ride not available'
            ]);
        }

        Log::info('driver_ride_details.found', [
            'ride_id'     => $ride->id,
            'driver_id'   => $driver->id,
            'ride_status' => $ride->status,
        ]);

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

        $tripDistance = $this->fareService->calculateDistance(
            $ride->pickup_lat,
            $ride->pickup_lng,
            $ride->drop_lat,
            $ride->drop_lng
        );
        $settings = $this->fareService->getSettings();
        $tripFareDetails = $this->fareService->calculateTripFare(
            $tripDistance,
            $settings,
            (int) ($ride->passenger_count ?? 1)
        );
        $waitingFareDetails = $this->fareService->calculateWaitingFare((int) ($ride->waiting_mins ?? 0), $settings);
        $ride->trip_distance_km = $tripFareDetails['actual_distance_km'];
        $ride->fare_amount = round(
            (float) $tripFareDetails['trip_fare'] + (float) ($ride->pickup_fare ?? 0) + (float) $waitingFareDetails['waiting_fare'],
            2
        );

        // Driver must NOT see start PIN — customer shares it verbally.
        $ride->makeHidden(['start_pin']);

        return response()->json([
            'status' => true,
            'data'   => $ride,
            'pin_required' => in_array($ride->status, ['accepted', 'arrived'], true),
        ]);
    }

    public function rideArrived(Request $request, $rideId)
    {
        $driver = Auth::user();
        if (!$driver) {
            return response()->json(['status' => false, 'message' => 'Driver not found']);
        }

        $ride = RideRequest::where('id', $rideId)
            ->where('driver_id', $driver->id)
            ->where('status', 'accepted')
            ->first();

        if (!$ride) {
            return response()->json(['status' => false, 'message' => 'Ride not ready for arrival']);
        }

        $ride->status = 'arrived';
        $ride->save();

        $this->flowService->autoStartWaiting($ride->fresh());

        $this->flowService->notifyCustomer(
            $ride,
            'Driver Arrived 🚖',
            ($driver->name ?? 'Your driver') . ' has reached your pickup location.',
            'driver_arrived',
            ['driver_name' => (string) ($driver->name ?? '')]
        );

        return response()->json([
            'status' => true,
            'message' => 'Arrival marked',
            'ride_id' => $ride->id,
            'ride_status' => 'arrived',
        ]);
    }

    /**
     * Driver can edit passenger count after arrive. Recalculates estimated fare.
     */
    public function updatePassengerCount(Request $request, $rideId)
    {
        $validated = $request->validate([
            'passenger_count' => 'required|integer|min:1|max:3',
        ]);

        $driver = Auth::user();
        $passengerCount = $this->fareService->normalizePassengerCount((int) $validated['passenger_count']);

        $ride = RideRequest::where('id', $rideId)
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'arrived'])
            ->whereIn('booking_type', ['instant', 'prebook', 'virtual_stand'])
            ->first();

        if (!$ride) {
            return response()->json([
                'status' => false,
                'message' => 'Passenger count can be edited only after accept / arrive.',
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

        $this->flowService->notifyCustomer(
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
            'estimated_fare' => round($estimatedFare, 2),
            'payment_method' => 'cash',
            'fare_breakdown' => [
                'passenger_count' => $passengerCount,
                'base_km' => $tripBreakdown['base_km'],
                'base_fare' => $tripBreakdown['base_fare'],
                'extra_billable_km' => $tripBreakdown['extra_distance_km'],
                'trip_per_km_rate' => $tripBreakdown['trip_per_km_rate'],
                'trip_fare' => $tripBreakdown['trip_fare'],
                'pickup_fare' => (float) ($ride->pickup_fare ?? 0),
                'waiting_fare' => (float) ($ride->waiting_fare ?? 0),
                'total_estimated_fare' => $estimatedFare,
            ],
        ]);
    }

    public function rideStart(Request $request,$rideId)
    {
        $request->validate([
            'pin' => 'required|digits:4',
        ]);

        $driver = Auth::user();
        if(!$driver){
            return response()->json([
                'status'  => false,
                'message' => 'Driver not found'
            ]);
        }
        $ride = RideRequest::where('id',$rideId)
                        ->where('driver_id',$driver->id)
                        ->whereIn('status', ['accepted', 'arrived'])
                        ->first();
        if(!$ride){
            return response()->json(['status'=>false,'message'=>'Ride not ready']);
        }

        if ((int) ($ride->start_pin_attempts ?? 0) >= 5) {
            return response()->json([
                'status' => false,
                'message' => 'Too many invalid PIN attempts. Ask customer to share the correct PIN or contact support.',
            ], 422);
        }

        if (empty($ride->start_pin) || (string) $ride->start_pin !== (string) $request->pin) {
            $ride->increment('start_pin_attempts');
            $attemptsLeft = max(0, 5 - (int) $ride->fresh()->start_pin_attempts);

            return response()->json([
                'status' => false,
                'message' => 'Invalid ride PIN',
                'attempts_left' => $attemptsLeft,
            ], 422);
        }

        $waitingDetails = $this->flowService->autoStopWaiting($ride->fresh());

        $ride->status = 'started';
        $ride->started_at = now();
        $ride->start_pin_verified_at = now();
        $ride->save();

        $this->flowService->notifyCustomer(
            $ride,
            'Ride Started 🛣️',
            'Your ride has started. Have a safe journey!',
            'ride_started',
            ['driver_name' => (string) ($driver->name ?? '')]
        );

        $response = [
            'status'  => true,
            'message' => 'Ride started',
            'ride_id' => $ride->id,
        ];

        if ($waitingDetails) {
            $response['waiting_mins'] = $waitingDetails['waiting_mins'];
            $response['waiting_fare'] = $waitingDetails['waiting_fare'];
        }

        return response()->json($response);
    }

    public function rideCompleted(Request $request)
    {
        $request->validate([
            'ride_id' => 'required',
            'collected_by' => 'nullable|in:hand,online',
        ]);
        $driver = Auth::user();
        if(!$driver){
            return response()->json([
                'status'  => false,
                'message' => 'Driver not found'
            ]);
        }
        $ride = RideRequest::where('id',$request->ride_id)
                        ->where('driver_id',$driver->id)
                        ->where('status','started')
                        ->first();
        if(!$ride){
            return response()->json(['status'=>false,'message'=>'Ride not ready']);
        }

        $collectedBy = $request->collected_by ?? 'hand';
        if (
            $collectedBy === 'online' &&
            $ride->payment_status !== 'paid'
        ) {
            return response()->json([
                'status' => false,
                'message' => 'Online payment is not verified. Use cash payment for now.',
            ], 422);
        }

        $ride->status       = 'completed';
        $ride->completed_at = now();
        $settings = $this->fareService->getSettings();
        $passengerCount = $this->fareService->normalizePassengerCount((int) ($ride->passenger_count ?? 1));

        if (in_array($ride->booking_type, ['hire', 'tour'], true)) {
            $tripFareDetails = [
                'actual_distance_km' => (float) ($ride->trip_distance_km ?? 0),
                'extra_distance_km' => 0,
                'billable_distance_km' => 0,
                'trip_fare' => (float) ($ride->trip_fare ?? $ride->estimated_fare ?? 0),
                'base_km' => (float) $settings['base_km'],
                'base_fare' => 0,
                'trip_per_km_rate' => 0,
            ];
        } else {
            $tripDistance = $this->fareService->calculateDistance(
                (float) $ride->pickup_lat,
                (float) $ride->pickup_lng,
                (float) $ride->drop_lat,
                (float) $ride->drop_lng
            );
            $tripFareDetails = $this->fareService->calculateTripFare($tripDistance, $settings, $passengerCount);
        }

        $waitingFareDetails = $this->fareService->calculateWaitingFare((int) ($ride->waiting_mins ?? 0), $settings);
        $finalFare = round(
            (float) $tripFareDetails['trip_fare'] + (float) ($ride->pickup_fare ?? 0) + (float) $waitingFareDetails['waiting_fare'],
            2
        );
        $ride->trip_distance_km = $tripFareDetails['actual_distance_km'];
        $ride->trip_fare = $tripFareDetails['trip_fare'];
        $ride->waiting_chargeable_mins = $waitingFareDetails['waiting_chargeable_mins'];
        $ride->waiting_fare = $waitingFareDetails['waiting_fare'];
        $ride->estimated_fare = $finalFare;
        $ride->fare = $finalFare;
        $ride->collected_by = $collectedBy === 'online' ? 'online' : 'hand';
        if ($ride->collected_by === 'hand') {
            $ride->payment_status = 'not_required';
        }
        $ride->save();

        $driver->update(['is_available' => 1]);

        $this->flowService->notifyCustomer(
            $ride,
            'Ride Completed ✅',
            'Your ride is completed. Total fare: ₹' . number_format($finalFare, 2) . ' (Cash)',
            'ride_completed',
            [
                'total_fare' => (string) $finalFare,
                'collected_by' => (string) ($ride->collected_by ?? 'hand'),
                'payment_method' => 'cash',
                'passenger_count' => (string) $passengerCount,
            ]
        );
        app(RideCommunicationService::class)->closeConversation($ride->fresh());

        return response()->json([
            'status'  => true,
            'message' => 'Ride Completed',
            'payment_method' => 'cash',
            'fare_breakdown' => [
                'passenger_count' => $passengerCount,
                'trip_distance_km' => $tripFareDetails['actual_distance_km'],
                'base_km' => $tripFareDetails['base_km'],
                'base_fare' => $tripFareDetails['base_fare'],
                'extra_billable_km' => $tripFareDetails['extra_distance_km'] ?? $tripFareDetails['billable_distance_km'],
                'trip_per_km_rate' => $tripFareDetails['trip_per_km_rate'],
                'trip_fare' => $tripFareDetails['trip_fare'],
                'pickup_fare' => (float) ($ride->pickup_fare ?? 0),
                'waiting_mins' => (int) ($ride->waiting_mins ?? 0),
                'waiting_chargeable_mins' => $waitingFareDetails['waiting_chargeable_mins'],
                'waiting_fare' => $waitingFareDetails['waiting_fare'],
                'total_fare' => $finalFare,
            ],
        ]);
    }

    public function createInstantPaymentOrder(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|integer',
        ]);

        $driver = Auth::user();
        if (!$driver) {
            return response()->json(['status' => false, 'message' => 'Driver not found']);
        }

        $ride = RideRequest::where('id', $request->ride_id)
            ->where('driver_id', $driver->id)
            ->where('booking_type', 'instant')
            ->where('status', 'started')
            ->first();

        if (!$ride) {
            return response()->json([
                'status' => false,
                'message' => 'Instant ride not eligible for online payment',
            ], 404);
        }

        $settings = $this->fareService->getSettings();
        $tripDistance = $this->fareService->calculateDistance(
            (float) $ride->pickup_lat,
            (float) $ride->pickup_lng,
            (float) $ride->drop_lat,
            (float) $ride->drop_lng
        );
        $tripFareDetails = $this->fareService->calculateTripFare(
            $tripDistance,
            $settings,
            (int) ($ride->passenger_count ?? 1)
        );
        $waitingFareDetails = $this->fareService->calculateWaitingFare((int) ($ride->waiting_mins ?? 0), $settings);
        $finalFare = round(
            (float) $tripFareDetails['trip_fare'] + (float) ($ride->pickup_fare ?? 0) + (float) $waitingFareDetails['waiting_fare'],
            2
        );

        $ride->trip_distance_km = $tripFareDetails['actual_distance_km'];
        $ride->trip_fare = $tripFareDetails['trip_fare'];
        $ride->waiting_chargeable_mins = $waitingFareDetails['waiting_chargeable_mins'];
        $ride->waiting_fare = $waitingFareDetails['waiting_fare'];
        $ride->estimated_fare = $finalFare;
        $ride->save();

        try {
            $order = $this->paymentService->createInstantRideOrder($ride, $finalFare);

            return response()->json([
                'status' => true,
                'message' => 'Instant payment order created',
                'ride_id' => $ride->id,
                'razorpay_order_id' => $order['order_id'],
                'razorpay_key' => $order['key'],
                'amount' => $order['amount'],
                'currency' => $order['currency'],
                'qr_code_id' => $order['qr_code_id'],
                'qr_code_url' => $order['qr_code_url'],
                'fare_breakdown' => [
                    'trip_fare' => $tripFareDetails['trip_fare'],
                    'pickup_fare' => (float) ($ride->pickup_fare ?? 0),
                    'waiting_fare' => $waitingFareDetails['waiting_fare'],
                    'total_fare' => $finalFare,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => safeApiMessage($e),
            ], 500);
        }
    }

    public function verifyInstantPayment(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|integer',
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
        ]);

        $driver = Auth::user();
        if (!$driver) {
            return response()->json(['status' => false, 'message' => 'Driver not found']);
        }

        $ride = RideRequest::where('id', $request->ride_id)
            ->where('driver_id', $driver->id)
            ->where('booking_type', 'instant')
            ->where('status', 'started')
            ->first();

        if (!$ride) {
            return response()->json([
                'status' => false,
                'message' => 'Instant ride not found',
            ], 404);
        }

        try {
            $this->paymentService->verifyAndCapture(
                $ride,
                $request->razorpay_order_id,
                $request->razorpay_payment_id,
                $request->razorpay_signature
            );

            $ride->collected_by = 'online';
            $ride->save();

            $paidAmount = (float) ($ride->advance_amount ?? $ride->estimated_fare ?? 0);

            $this->flowService->notifyCustomer(
                $ride,
                'Payment Received 💳',
                '₹' . number_format($paidAmount, 2) . ' received for your ride.',
                'payment_received',
                [
                    'amount' => (string) $paidAmount,
                    'payment_status' => 'paid',
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Instant payment verified successfully',
                'ride_id' => $ride->id,
                'payment_status' => 'paid',
                'collected_by' => 'online',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'message' => 'Payment verification failed: ' . safeApiMessage($e),
            ], 422);
        }
    }

    public function getDriverRideHistory(Request $request)
    {
        $driver = Auth::user();
        if(!$driver){
            return response()->json([
                'status'    => false,
                'message'   => 'Driver not found'
            ]);
        }
        $rides = RideRequest::with([
                'customer:id,name,phone'
            ])
            ->select('id','customer_id','pickup','drop','status','created_at')
            ->where('driver_id', $driver->id)
            ->whereIn('status', ['completed','cancelled'])
            ->orderBy('id','desc')
            ->get();

        $data = $rides->map(function ($ride) {
            return [
                'id'         => (int) $ride->id,
                'pickup'     => $ride->pickup,
                'drop'       => $ride->drop,
                'status'     => $ride->status,
                'created_at' => optional($ride->created_at)->toIso8601String(),

                'customer'   => [
                    'name'  => $ride->customer->name ?? null,
                    'phone' => $ride->customer->phone ?? null,
                ],
            ];
        });    
        return response()->json([
            'status' => true,
            'data'   => $data
        ]);
    }

    public function driverCloseReason()
    {
        $reasons = CancelReason::where('type', 'D')->pluck('name'); // only names as array
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

    public function driverCancelRide(Request $request)
    {
        $request->validate([
            'ride_id' => 'required',
            'reason'  => 'required'
        ]);

        $driver = auth()->user();

        DB::beginTransaction();

        try {

            $ride = RideRequest::where('id', $request->ride_id)
                ->where('driver_id', $driver->id)
                ->whereIn('status', ['accepted','arrived'])
                ->lockForUpdate()
                ->first();

            if (!$ride) {
                return response()->json([
                    'status' => false,
                    'message'=> 'Ride cannot be cancelled'
                ]);
            }

            $this->flowService->autoStopWaiting($ride);

            $ride->update([
                'status'        => 'cancelled',
                'cancelled_by'  => 'driver',
                'cancel_reason' => $request->reason,
                'cancelled_at'  => now()
            ]);
            app(RideCommunicationService::class)->closeConversation($ride->fresh());

            //notification for cancel
            $this->flowService->notifyCustomer(
                $ride,
                'Ride Cancelled 🚫',
                'Driver has cancelled your ride. Please book again.',
                'ride_cancelled',
                [
                    'cancelled_by' => 'driver',
                    'cancel_reason' => (string) $request->reason,
                ]
            );

            // driver back to available
            $driver->update([
                'is_available' => 1
            ]);
            

            DB::commit();

            return response()->json([
                'status' => true,
                'message'=> 'Ride cancelled by driver'
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

    public function toggleFairPrice(Request $request)
    {
        $request->validate([
            'status' => 'required|boolean'
        ]);
        $user = auth()->user();

        $turningOff = !$request->boolean('status');
        if ($turningOff) {
            $activeRide = RideRequest::where('driver_id', $user->id)
                ->whereIn('status', ['accepted', 'arrived', 'started'])
                ->exists();

            if ($activeRide) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot turn off FairPrice while you have an active ride. Complete or cancel the ride first.',
                ], 422);
            }
        }

        $user->fair_price_enabled = $request->status;
        $user->is_available       = $request->status ? 1 : 0;
        $user->is_online          = $request->status ? 1 : 0;
        $user->save();

        $user->load([
            'userInfo.autoBrand',
            'userInfo.autoModel',
            'userInfo.autoFueltype'
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Fair price status updated successfully',

            'data' => [
                'name'                  => $user->name,
                'phone_number'          => $user->phone_number,
                'fair_price_enabled'    => $user->fair_price_enabled,

                'user_info' => [
                    'dob'                       => $user->userInfo->dob ?? null,
                    'gender'                    => $user->userInfo->gender ?? null,
                    'blood_group'               => $user->userInfo->blood_group ?? null,

                    'brand'                     => $user->userInfo->autoBrand->brand_name ?? null,
                    'model'                     => $user->userInfo->autoModel->model_name ?? null,
                    'fuel'                      => $user->userInfo->autoFueltype->name ?? null,
                    'brand_id'                  => $user->userInfo->brand_id ?? null,
                    'model_id'                  => $user->userInfo->model_id ?? null,
                    'fuel_id'                   => $user->userInfo->fuel_id ?? null,
                    'millage'                   => $user->userInfo->millage ?? null,
                    'vehicle_no'                => $user->userInfo->vehicle_no ?? null,

                    'seating_capacity'          => $user->userInfo->seating_capacity ?? null,

                    'driving_license_no'        => $user->userInfo->driving_license_no ?? null,
                    'driving_license_expiry'    => $user->userInfo->driving_license_expiry ?? null,

                    'rc_book_no'                => $user->userInfo->rc_book_no ?? null,

                    'insurance_expiry'          => $user->userInfo->insurance_expiry ?? null,

                    'permit_no'                 => $user->userInfo->permit_no ?? null,
                    'permit_expiry'             => $user->userInfo->permit_expiry ?? null,
                ]
            ]
        ]);

        // return response()->json([
        //     'status'                => true,
        //     'fair_price_enabled'    => $user->fair_price_enabled
        // ]);
    }

    public function rideHistory(Request $request)
    {
        $driver = Auth::user();

        $totalRides = RideRequest::where('driver_id', $driver->id)->count();

        $completedRides = RideRequest::where('driver_id', $driver->id)
                                ->where('status', 'completed')
                                ->count();

        $cancelledRides = RideRequest::where('driver_id', $driver->id)
                                ->where('status', 'cancelled')
                                ->count();

        $totalEarnings = 0;

        $rides = RideRequest::with('customer:id,name')
                                ->where('driver_id', $driver->id)
                                ->latest()
                                ->get();
        

        $rides->transform(function ($ride) use (&$totalEarnings) {

                $distance = $this->fareService->calculateDistance(
                    $ride->pickup_lat,
                    $ride->pickup_lng,
                    $ride->drop_lat,
                    $ride->drop_lng
                );
                $settings = $this->fareService->getSettings();
                $tripFareDetails = $this->fareService->calculateTripFare(
                    $distance,
                    $settings,
                    (int) ($ride->passenger_count ?? 1)
                );
                $waitingFareDetails = $this->fareService->calculateWaitingFare((int) ($ride->waiting_mins ?? 0), $settings);
                $fare = round((float) $tripFareDetails['trip_fare'] + (float) ($ride->pickup_fare ?? 0) + (float) $waitingFareDetails['waiting_fare'], 2);

                if ($ride->status == 'completed') {
                    $totalEarnings += $fare;
                }

                return [
                    'ride_id'           => $ride->id,
                    'customer_name'     => optional($ride->customer)->name,
                    'pickup'            => $ride->pickup,
                    'drop'              => $ride->drop,
                    'ride_for'          => ucfirst(str_replace('_', ' ', $ride->ride_for)),
                    'passenger_count'   => $ride->passenger_count,
                    'distance'          => $distance,
                    'fare'              => round($fare, 2),
                    'date'              => date('d-m-Y', strtotime($ride->date)),
                    'status'            => ucfirst($ride->status),
                ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Ride history fetched successfully.',
            'summary' => [
                'total_rides'       => $totalRides,
                'completed_rides'   => $completedRides,
                'cancelled_rides'   => $cancelledRides,
                'total_earnings'    => round($totalEarnings, 2),
            ],
            'rides' => $rides
        ]);
    }

}
