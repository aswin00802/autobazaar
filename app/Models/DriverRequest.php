<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Masters\AutoAreas;

class DriverRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'number',
        'area_id',
        'status',
    ];
    public function autoArea()
{
    return $this->belongsTo(AutoAreas::class, 'area_id', 'id');
}

    
}

