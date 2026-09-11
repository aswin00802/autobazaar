<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosAlert extends Model
{
    protected $fillable = [
        'ride_id',
        'triggered_by',
        'triggered_by_id',
        'latitude',
        'longitude',
        'address',
        'description',
        'status',
        'resolved_at',
    ];

    public function ride()
    {
        return $this->belongsTo(RideRequest::class,'ride_id');
    }

}
