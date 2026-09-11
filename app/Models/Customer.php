<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasApiTokens;
    protected $fillable = [
        'name',
        'f_name',
        'l_name',
        'username',
        'email',
        'phone',
        'gender',
        'dob',
        'password',
        'location',
        'profile',
        'device_token',
        'device_id',
        'role_id',
        'last_login',
        'current_location',
        'status_id',
        'latitude',
        'longitude',
        'family_details',
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'family_details' => 'array',
    ];

    public function customerRides()
    {
        return $this->hasMany(RideRequest::class,'customer_id');
    }
}
