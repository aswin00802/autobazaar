<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FairpriceFareSetting extends Model
{
    protected $fillable = [
        'trip_per_km_rate',
        'pickup_per_km_rate',
        'free_pickup_km',
        'base_fare',
        'base_km',
        'passenger_1_base_fare',
        'passenger_1_per_km',
        'passenger_2_base_fare',
        'passenger_2_per_km',
        'passenger_3_base_fare',
        'passenger_3_per_km',
        'min_billable_trip_km',
        'waiting_free_mins',
        'waiting_per_min_rate',
        'tour_per_km_rate',
        'tour_min_km',
        'hire_daily_per_km_rate',
        'hire_monthly_per_km_rate',
        'hire_tour_advance_percent',
        'is_active',
    ];
}
