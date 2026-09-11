<?php

namespace App\Services;

use App\Models\FairpriceFareSetting;
use Carbon\Carbon;

class FairPriceFareService
{
    public function getSettings(): array
    {
        $settings = FairpriceFareSetting::where('is_active', 1)->latest('id')->first();

        $passenger1Base = (float) ($settings->passenger_1_base_fare ?? $settings->base_fare ?? 50);
        $passenger1PerKm = (float) ($settings->passenger_1_per_km ?? $settings->trip_per_km_rate ?? 10);

        return [
            'trip_per_km_rate' => (float) ($settings->trip_per_km_rate ?? $passenger1PerKm),
            'pickup_per_km_rate' => (float) ($settings->pickup_per_km_rate ?? 18),
            'free_pickup_km' => (float) ($settings->free_pickup_km ?? 2),
            'base_fare' => (float) ($settings->base_fare ?? $passenger1Base),
            'base_km' => (float) ($settings->base_km ?? 1.8),
            'passenger_1_base_fare' => $passenger1Base,
            'passenger_1_per_km' => $passenger1PerKm,
            'passenger_2_base_fare' => (float) ($settings->passenger_2_base_fare ?? 60),
            'passenger_2_per_km' => (float) ($settings->passenger_2_per_km ?? 20),
            'passenger_3_base_fare' => (float) ($settings->passenger_3_base_fare ?? 70),
            'passenger_3_per_km' => (float) ($settings->passenger_3_per_km ?? 30),
            'min_billable_trip_km' => (float) ($settings->min_billable_trip_km ?? 2),
            'waiting_free_mins' => (int) ($settings->waiting_free_mins ?? 5),
            'waiting_per_min_rate' => (float) ($settings->waiting_per_min_rate ?? 2),
            'tour_per_km_rate' => (float) ($settings->tour_per_km_rate ?? 18),
            'tour_min_km' => (float) ($settings->tour_min_km ?? 200),
            'hire_daily_per_km_rate' => (float) ($settings->hire_daily_per_km_rate ?? 18),
            'hire_monthly_per_km_rate' => (float) ($settings->hire_monthly_per_km_rate ?? 15),
            'hire_tour_advance_percent' => (int) ($settings->hire_tour_advance_percent ?? 50),
        ];
    }

    public function normalizePassengerCount(int $count): int
    {
        return max(1, min(3, $count));
    }

    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) *
            cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * City trip fare using passenger slabs (1–3).
     * Base fare covers up to base_km; then passenger-specific per-km rate.
     */
    public function calculateTripFare(float $distanceKm, array $settings, int $passengerCount = 1): array
    {
        $passengerCount = $this->normalizePassengerCount($passengerCount);
        $actualDistance = round($distanceKm, 2);
        $baseKm = (float) $settings['base_km'];
        $baseFare = (float) ($settings["passenger_{$passengerCount}_base_fare"]
            ?? $settings['base_fare']
            ?? 50);
        $rate = (float) ($settings["passenger_{$passengerCount}_per_km"]
            ?? $settings['trip_per_km_rate']
            ?? 10);

        if ($actualDistance <= $baseKm) {
            return [
                'actual_distance_km' => $actualDistance,
                'billable_distance_km' => 0,
                'extra_distance_km' => 0,
                'base_km' => $baseKm,
                'base_fare' => round($baseFare, 2),
                'trip_per_km_rate' => $rate,
                'passenger_count' => $passengerCount,
                'trip_fare' => round($baseFare, 2),
                'base_fare_applied' => true,
            ];
        }

        $extraDistance = round($actualDistance - $baseKm, 2);
        $fare = $baseFare + ($extraDistance * $rate);

        return [
            'actual_distance_km' => $actualDistance,
            'billable_distance_km' => $extraDistance,
            'extra_distance_km' => $extraDistance,
            'base_km' => $baseKm,
            'base_fare' => round($baseFare, 2),
            'trip_per_km_rate' => $rate,
            'passenger_count' => $passengerCount,
            'trip_fare' => round($fare, 2),
            'base_fare_applied' => false,
        ];
    }

    public function calculatePickupFare(float $pickupDistanceKm, array $settings): array
    {
        $freePickupKm = (float) ($settings['free_pickup_km'] ?? 2);
        $rate = (float) $settings['pickup_per_km_rate'];
        $actualDistance = round($pickupDistanceKm, 2);

        if ($actualDistance <= $freePickupKm) {
            return [
                'actual_distance_km' => $actualDistance,
                'billable_distance_km' => 0,
                'pickup_fare' => 0,
                'free_pickup_applied' => true,
            ];
        }

        $billableDistance = round($actualDistance - $freePickupKm, 2);

        return [
            'actual_distance_km' => $actualDistance,
            'billable_distance_km' => $billableDistance,
            'pickup_fare' => round($billableDistance * $rate, 2),
            'free_pickup_applied' => true,
        ];
    }

    public function calculateWaitingFare(int $totalWaitingMins, array $settings): array
    {
        $freeMins = (int) $settings['waiting_free_mins'];
        $chargeableMins = max(0, $totalWaitingMins - $freeMins);
        $waitingFare = $chargeableMins * (float) $settings['waiting_per_min_rate'];

        return [
            'waiting_mins' => $totalWaitingMins,
            'waiting_chargeable_mins' => $chargeableMins,
            'waiting_fare' => round($waitingFare, 2),
        ];
    }

    /**
     * Round-trip approx fare for hire/tour: one-way * 2, with tour min km on one-way.
     */
    public function calculateRoundTripFare(
        float $oneWayKm,
        string $bookingType,
        ?string $hireType,
        array $settings
    ): array {
        $oneWayKm = round($oneWayKm, 2);

        if ($bookingType === 'tour') {
            $minKm = (float) $settings['tour_min_km'];
            $rate = (float) $settings['tour_per_km_rate'];
            $billableOneWay = max($oneWayKm, $minKm);
        } else {
            $rate = $hireType === 'monthly'
                ? (float) $settings['hire_monthly_per_km_rate']
                : (float) $settings['hire_daily_per_km_rate'];
            $billableOneWay = $oneWayKm;
            $minKm = 0;
        }

        $roundTripKm = round($billableOneWay * 2, 2);
        $estimatedFare = round($roundTripKm * $rate, 2);
        $advancePercent = (int) $settings['hire_tour_advance_percent'];
        $advanceAmount = round($estimatedFare * ($advancePercent / 100), 2);

        return [
            'one_way_km' => $oneWayKm,
            'billable_one_way_km' => round($billableOneWay, 2),
            'round_trip_km' => $roundTripKm,
            'per_km_rate' => $rate,
            'min_km_applied' => $bookingType === 'tour' && $billableOneWay > $oneWayKm,
            'estimated_fare' => $estimatedFare,
            'advance_percent' => $advancePercent,
            'advance_amount' => $advanceAmount,
            'is_round_trip' => true,
        ];
    }

    /**
     * Cancel refund slab for hire/tour based on days remaining until scheduled_at.
     * >=5 full, 4 => 10%, 3 => 25%, 2 => 50%, <=1 => 100% (no refund).
     */
    public function calculateHireTourRefund(float $advancePaid, $scheduledAt): array
    {
        if (!$scheduledAt) {
            return [
                'days_remaining' => 0,
                'deduction_percent' => 100,
                'refund_amount' => 0,
                'refundable' => false,
            ];
        }

        $scheduled = $scheduledAt instanceof Carbon ? $scheduledAt : Carbon::parse($scheduledAt);
        $daysRemaining = (int) now()->startOfDay()->diffInDays($scheduled->copy()->startOfDay(), false);

        if ($daysRemaining >= 5) {
            $deduction = 0;
        } elseif ($daysRemaining === 4) {
            $deduction = 10;
        } elseif ($daysRemaining === 3) {
            $deduction = 25;
        } elseif ($daysRemaining === 2) {
            $deduction = 50;
        } else {
            $deduction = 100;
        }

        $refundAmount = round($advancePaid * ((100 - $deduction) / 100), 2);

        return [
            'days_remaining' => max(0, $daysRemaining),
            'deduction_percent' => $deduction,
            'refund_amount' => $refundAmount,
            'refundable' => $refundAmount > 0,
        ];
    }
}
