<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Model;

class VehicleSuitability extends Model
{
    protected $table = 'vehicle_suitability';
    protected $guarded = [];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }
}
