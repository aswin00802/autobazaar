<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Model;

class VehiclePrice extends Model
{
    protected $table = 'vehicle_prices';
    protected $guarded = [];
    protected $casts = ['is_default' => 'boolean'];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function variant()
    {
        return $this->belongsTo(VehicleVariant::class, 'vehicle_variant_id');
    }

    public function getOnRoadAttribute(): float
    {
        return (float) $this->ex_showroom + $this->rto + $this->insurance + $this->registration + $this->other + $this->accessories;
    }
}
