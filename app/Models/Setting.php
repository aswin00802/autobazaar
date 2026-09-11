<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $primarykey = 'id';

    protected $fillable = [
        'key',
        'value',
        'type',
        'status_id',
        'created_by',
        'ip_address',
    ];

    protected $casts = [
        'gateway_settings' => 'array', // or 'json'
    ];
}
