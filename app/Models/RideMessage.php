<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RideMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'ride_id',
        'sender_type',
        'sender_id',
        'message_type',
        'text',
        'media_path',
        'media_mime',
        'media_size_bytes',
        'duration_sec',
        'is_deleted',
        'customer_read_at',
        'driver_read_at',
    ];

    protected $casts = [
        'conversation_id' => 'integer',
        'ride_id' => 'integer',
        'sender_id' => 'integer',
        'media_size_bytes' => 'integer',
        'duration_sec' => 'integer',
        'is_deleted' => 'boolean',
        'customer_read_at' => 'datetime',
        'driver_read_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(RideConversation::class, 'conversation_id');
    }
}

