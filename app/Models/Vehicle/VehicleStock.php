<?php

namespace App\Models\Vehicle;

use App\Models\Masters\AuthorizedSeller;
use Illuminate\Database\Eloquent\Model;

class VehicleStock extends Model
{
    protected $table = 'vehicle_stock';
    protected $guarded = [];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function variant()
    {
        return $this->belongsTo(VehicleVariant::class, 'vehicle_variant_id');
    }

    public function dealer()
    {
        return $this->belongsTo(AuthorizedSeller::class, 'dealer_id');
    }
}
