<?php

namespace App\Models\Vehicle;

use App\Models\Masters\AutoFuelType;
use Illuminate\Database\Eloquent\Model;

class VehicleVariant extends Model
{
    protected $table = 'vehicle_variants';
    protected $guarded = [];
    protected $casts = ['is_default' => 'boolean', 'mileage' => 'float', 'ex_showroom_price' => 'float'];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function fuelType()
    {
        return $this->belongsTo(AutoFuelType::class, 'fuel_type_id');
    }
}
