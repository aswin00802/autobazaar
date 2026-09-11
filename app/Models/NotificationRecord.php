<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationRecord extends Model
{
    protected $fillable = ['date','payload'];

    protected $casts = [
        'payload' => 'array'
    ];
}
