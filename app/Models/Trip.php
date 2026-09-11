<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'user_id',
        'target_id',
        'amount',
        'source',
    ];

    public function target()
    {
        return $this->belongsTo(Target::class,'target_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
