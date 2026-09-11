<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\RideRequest;
use App\Models\RideRequestDriver;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class FairPriceRideDispatchService
{
    public function dispatch(RideRequest $ride, Customer $customer): int
    {
        $requireOnlineAvailable = $ride->booking_type === 'instant';
        $drivers = $this->findDriversByFuel(
            $ride->fuel_id ?? null,
            (float) $ride->pickup_lat,
            (float) $ride->pickup_lng,
            $customer->phone,
            $requireOnlineAvailable
        );

        foreach ($drivers as $driver) {
            RideRequestDriver::firstOrCreate([
                'ride_id' => $ride->id,
                'driver_id' => $driver->id,
            ], [
                'status' => 'pending',
            ]);
        }

        if ($drivers->isEmpty()) {
            Log::warning('fairprice_dispatch.no_drivers_found', [
                'ride_id' => $ride->id,
            ]);

            return 0;
        }

        $this->notifyDrivers(
            $drivers,
            $customer,
            $ride,
            'New Ride Request',
            $customer->name .
            " requested a ride near your location.\n" .
            "Ride For: {$ride->ride_for}\n" .
            "Passengers: {$ride->passenger_count}\n" .
            "Destination: {$ride->drop}\n" .
            "Tap to accept this ride request."
        );

        Log::info('fairprice_dispatch.completed', [
            'ride_id' => $ride->id,
            'driver_count' => $drivers->count(),
        ]);

        return $drivers->count();
    }

    public function findDriversWithinKm(
        float $lat,
        float $lng,
        float $radiusKm = 1,
        array $excludeDriverIds = [],
        ?string $excludePhone = null
    ): Collection
    {
        $driversQuery = User::with('userInfo')
            ->select('users.*')
            ->selectRaw('(6371 * acos(cos(radians(?))
                * cos(radians(latitude))
                * cos(radians(longitude) - radians(?))
                + sin(radians(?))
                * sin(radians(latitude)))) AS distance', [$lat, $lng, $lat])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance', '<=', $radiusKm)
            ->orderBy('distance');

        if (!empty($excludeDriverIds)) {
            $driversQuery->whereNotIn('id', $excludeDriverIds);
        }
        if (!empty($excludePhone)) {
            $driversQuery->where('phone_number', '!=', $excludePhone);
        }

        // Virtual Stand must show only currently active drivers.
        if (Schema::hasColumn('users', 'status')) {
            $driversQuery->where('status', 1);
        }
        if (Schema::hasColumn('users', 'is_online')) {
            $driversQuery->where('is_online', 1);
        }
        if (Schema::hasColumn('users', 'is_available')) {
            $driversQuery->where('is_available', 1);
        }

        if (Schema::hasColumn('users', 'fair_price_enabled')) {
            $driversQuery->where('fair_price_enabled', 1);
        } elseif (Schema::hasColumn('users', 'fare_price_enabled')) {
            $driversQuery->where('fare_price_enabled', 1);
        }

        return $driversQuery->get();
    }

    public function dispatchToDriver(RideRequest $ride, Customer $customer, int $driverId): bool
    {
        $driver = User::with('userInfo')->find($driverId);
        if (!$driver) {
            return false;
        }
        if (!empty($customer->phone) && (string) $driver->phone_number === (string) $customer->phone) {
            return false;
        }

        RideRequestDriver::updateOrCreate(
            [
                'ride_id' => $ride->id,
                'driver_id' => $driver->id,
            ],
            [
                'status' => 'pending',
            ]
        );

        $ride->update([
            'target_driver_id' => $driver->id,
            'status' => 'pending',
        ]);

        $this->notifyDrivers(
            collect([$driver]),
            $customer,
            $ride,
            'Virtual Stand Ride Request',
            $customer->name .
            " selected you from Virtual Auto Stand.\n" .
            "Destination: {$ride->drop}\n" .
            "Tap to accept or reject this request."
        );

        return true;
    }

    private function notifyDrivers(Collection $drivers, Customer $customer, RideRequest $ride, string $title, string $body): void
    {
        $tokens = $drivers->pluck('device_token')->filter()->values()->toArray();
        $userIds = $drivers->pluck('id')->values()->toArray();

        if (empty($tokens)) {
            return;
        }

        $dataPayload = [
            'type' => 'ride_v6',
            'ride_id' => $ride->id,
            'booking_type' => (string) $ride->booking_type,
            'url' => 'fareprice/ride-details/' . $ride->id,
            'android_channel_id' => 'normal_v5',
        ];

        app(CommonFirebaseNotification::class)->sendCommonNotification(
            $tokens,
            $title,
            $body,
            ['payload' => json_encode($dataPayload)],
            $userIds
        );
    }

    private function findDriversByFuel($fuelId, float $lat, float $lng, ?string $excludePhone = null, bool $requireOnlineAvailable = false): Collection
    {
        $radiuses = [1, 2, 5, 10];

        foreach ($radiuses as $radius) {
            $driversQuery = User::with('userInfo')
                ->select('users.*')
                ->selectRaw('(6371 * acos(cos(radians(?))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?))
                    * sin(radians(latitude)))) AS distance', [$lat, $lng, $lat])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->having('distance', '<=', $radius)
                ->orderBy('distance');

            if (Schema::hasColumn('users', 'fair_price_enabled')) {
                $driversQuery->where('fair_price_enabled', 1);
            } elseif (Schema::hasColumn('users', 'fare_price_enabled')) {
                $driversQuery->where('fare_price_enabled', 1);
            }

            // Instant only: online + available + active
            if ($requireOnlineAvailable) {
                if (Schema::hasColumn('users', 'status')) {
                    $driversQuery->where('status', 1);
                }
                if (Schema::hasColumn('users', 'is_online')) {
                    $driversQuery->where('is_online', 1);
                }
                if (Schema::hasColumn('users', 'is_available')) {
                    $driversQuery->where('is_available', 1);
                }
            }
            
            if (!empty($excludePhone)) {
                $driversQuery->where('phone_number', '!=', $excludePhone);
            }

            $drivers = $driversQuery->get();

            if ($drivers->count() > 0) {
                return $drivers;
            }
        }

        return collect();
    }
}
