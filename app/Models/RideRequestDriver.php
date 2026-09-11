<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideRequestDriver extends Model
{
    protected $fillable = [
        'ride_id',
        'driver_id',
        'status'
    ];

    public function ride()
    {
        return $this->belongsTo(RideRequest::class,'ride_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class,'driver_id');
    }
}
