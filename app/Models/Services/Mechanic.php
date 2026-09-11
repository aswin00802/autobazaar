<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Masters\AutoFuelType;

class Mechanic extends Model
{
    use HasFactory;
    protected $table = 'auto_mechanic';
    protected $guarded = [];

}
