<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\RideRequest;
use Carbon\Carbon;

class FairPriceRideFlowService
{
    public function __construct(
        private FairPriceFareService $fareService,
        private FarePriceFirebaseNotification $firebase,
    ) {}

    public function autoStartWaiting(RideRequest $ride): bool
    {
        if ($ride->waiting_started_at || $ride->waiting_ended_at) {
            return false;
        }

        $ride->waiting_started_at = now();
        $ride->save();

        return true;
    }

    public function autoStopWaiting(RideRequest $ride): ?array
    {
        if (!$ride->waiting_started_at || $ride->waiting_ended_at) {
            return null;
        }

        $waitingEnd = now();
        $totalWaitingMins = Carbon::parse($ride->waiting_started_at)->diffInMinutes($waitingEnd);
        $settings = $this->fareService->getSettings();
        $waitingFareDetails = $this->fareService->calculateWaitingFare((int) $totalWaitingMins, $settings);

        $ride->waiting_ended_at = $waitingEnd;
        $ride->waiting_mins = $waitingFareDetails['waiting_mins'];
        $ride->waiting_chargeable_mins = $waitingFareDetails['waiting_chargeable_mins'];
        $ride->waiting_fare = $waitingFareDetails['waiting_fare'];
        $ride->estimated_fare = round(
            (float) ($ride->trip_fare ?? 0) + (float) ($ride->pickup_fare ?? 0) + (float) $ride->waiting_fare,
            2
        );
        $ride->save();

        return $waitingFareDetails;
    }

    public function notifyCustomer(
        RideRequest $ride,
        string $title,
        string $body,
        string $type,
        array $extra = []
    ): void {
        $customer = Customer::find($ride->customer_id);

        if (!$customer || empty($customer->device_token)) {
            return;
        }

        $dataPayload = array_merge([
            'type' => $type,
            'ride_id' => (string) $ride->id,
            'android_channel_id' => 'customer_channel',
        ], $extra);

        $this->firebase->sendCommonNotification(
            [$customer->device_token],
            $title,
            $body,
            ['payload' => json_encode($dataPayload)],
            [$customer->id]
        );
    }

    public function notifyDriver(
        RideRequest $ride,
        string $title,
        string $body,
        string $type,
        array $extra = []
    ): void {
        if (empty($ride->driver_id)) {
            return;
        }

        $driver = $ride->driver ?? $ride->driver()->first();

        if (!$driver || empty($driver->device_token)) {
            return;
        }

        $dataPayload = array_merge([
            'type' => $type,
            'ride_id' => (string) $ride->id,
            'android_channel_id' => 'normal_v5',
        ], $extra);

        app(CommonFirebaseNotification::class)->sendCommonNotification(
            [$driver->device_token],
            $title,
            $body,
            ['payload' => json_encode($dataPayload)],
            [$driver->id]
        );
    }
}
