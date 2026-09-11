<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoFuelType extends Model
{
    use HasFactory;
    protected $table = 'auto_fuel_types';
    protected $guarded = [];
}
