<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Model;

class VehicleDocument extends Model
{
    protected $table = 'vehicle_documents';
    protected $guarded = [];

    public const TYPES = ['brochure' => 'Brochure', 'price_list' => 'Price List', 'specification' => 'Specification Sheet', 'other' => 'Other'];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }
}
