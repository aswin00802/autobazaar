<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Masters\AutoFuelType;

class GasStation extends Model
{
    use HasFactory;
    protected $table = 'auto_gas_stations';
    protected $guarded = [];
    
    public function fuel(){
        return $this->belongsTo(AutoFuelType::class, 'fuel_id', 'id');
        
    }
}
