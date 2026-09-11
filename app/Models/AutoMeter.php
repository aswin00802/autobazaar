<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AutoMeter extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_no',
        'invoice_inc_id',
        'from_location',
        'to_location',
        'total_km', 
        'total_time',
        'km_amount', 
        'fuel_amount', 
        'friction_amount', 
        'wages_amount', 
        'tips_amount', 
        'margin_amount', 
        'total_amount', 
        'date',
        'status_id',
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
}
