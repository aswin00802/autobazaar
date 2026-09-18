<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Model;

class VehicleScore extends Model
{
    protected $table = 'vehicle_scores';
    protected $guarded = [];
    protected $casts = ['score' => 'float'];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }
}
