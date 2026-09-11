<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $fillable = [
        'user_id',
        'target_amount',
        'date',
        'meter_amount',
        'earning_amount',
        'total_amount',
        'is_completed',
        'status_id',
    ];

    public function trips()
    {
        return $this->hasMany(Trip::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
