<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RideConversation extends Model
{
    protected $fillable = [
        'ride_id',
        'customer_id',
        'driver_id',
        'status',
        'closed_at',
    ];

    protected $casts = [
        'ride_id' => 'integer',
        'customer_id' => 'integer',
        'driver_id' => 'integer',
        'closed_at' => 'datetime',
    ];

    public function ride(): BelongsTo
    {
        return $this->belongsTo(RideRequest::class, 'ride_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(RideMessage::class, 'conversation_id');
    }
}

