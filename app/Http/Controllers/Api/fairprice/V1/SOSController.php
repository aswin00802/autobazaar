<?php

namespace App\Http\Controllers\Api\fairprice\V1;

use App\Http\Controllers\Controller;
use App\Models\RideRequest;
use App\Models\SosAlert;
use App\Models\User;
use App\Services\CommonFirebaseNotification;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SOSController extends Controller
{
    public function sendSOS(Request $request, SmsService $smsService)
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

        $customer = auth('customer')->user();
        $ride = RideRequest::where('id', $validatedData['ride_id'])
            ->where('customer_id', $customer->id)
            ->whereIn('status', ['accepted', 'arrived', 'started'])
            ->first();

        if (!$ride) {
            return response()->json([
                'status'  => false,
                'message' => 'Active ride not found for this customer.',
            ], 404);
        }

        $existingSos = SosAlert::where('ride_id', $ride->id)
            ->where('triggered_by', 'customer')
            ->first();

        if ($existingSos) {
            return response()->json([
                'status'       => true,
                'message'      => 'SOS already sent for this ride',
                'sos_id'       => $existingSos->id,
                'already_sent' => true,
            ]);
        }

        $sos = SosAlert::create([
            'ride_id'         => $ride->id,
            'triggered_by'    => 'customer',
            'triggered_by_id' => $customer->id,
            'latitude'        => $validatedData['latitude'],
            'longitude'       => $validatedData['longitude'],
            'description'     => $validatedData['description'] ?? null,
        ]);

        try {
            $adminUser = User::where('id', 1846)->whereNotNull('device_token')->first();
            $adminToken = (string) ($adminUser->device_token ?? '');
            $customerToken = (string) ($customer->device_token ?? '');

            if ($adminUser && $adminToken !== '' && $adminToken !== $customerToken) {
                $firebase = new CommonFirebaseNotification();
                $firebase->sendCommonNotification(
                    [$adminToken],
                    'Fairprice Emergency SOS',
                    ($customer->name ?: 'Customer') . ' triggered emergency SOS alert.',
                    [
                        'payload' => json_encode([
                            'type'               => 'sos_alert',
                            'triggered_by'       => 'customer',
                            'ride_id'            => $ride->id,
                            'sos_id'             => $sos->id,
                            'url'                => 'customer/sos-alert/view/' . $sos->id,
                            'android_channel_id' => 'emergency_alert',
                        ]),
                    ],
                    [$adminUser->id]
                );
            }
        } catch (\Throwable $e) {
            Log::error('FairPrice SOS admin notification failed.', [
                'sos_id' => $sos->id,
                'error' => safeApiMessage($e),
            ]);
        }

        $personName = trim((string) ($customer->name ?? '')) ?: 'Customer';
        $callPhone = preg_replace('/\D+/', '', (string) ($customer->phone ?? ''));
        $locationText = sprintf(
            'maps.google.com/?q=%s,%s',
            $validatedData['latitude'],
            $validatedData['longitude']
        );

        $smsSentCount = 0;
        $smsFailedCount = 0;

        foreach ($customer->family_details ?? [] as $member) {
            $mobile = (string) ($member['mobile'] ?? '');

            if (!preg_match('/^\d{10}$/', $mobile) || ($callPhone !== '' && $mobile === $callPhone)) {
                continue;
            }

            if ($smsService->sendSos($mobile, $personName, $locationText, $callPhone ?: $mobile)) {
                $smsSentCount++;
            } else {
                $smsFailedCount++;
            }
        }

        return response()->json([
            'status'           => true,
            'message'          => 'SOS alert sent successfully',
            'sos_id'           => $sos->id,
            'already_sent'     => false,
            'sms_sent_count'   => $smsSentCount,
            'sms_failed_count' => $smsFailedCount,
        ]);
    }

    public function viewSOS($id)
    {
        $sos = SosAlert::with([
            'ride.customer',
            'ride.driver:id,name,phone_number',
            'ride.driver.userInfo',
        ])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $sos,
        ]);
    }
}
