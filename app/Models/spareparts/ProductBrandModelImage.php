<?php

namespace App\Models\spareparts;

use Illuminate\Database\Eloquent\Model;

class ProductBrandModelImage extends Model
{
    protected $fillable = ['product_brand_model_id','image'];

    public function productBrandModel()
    {
        return $this->belongsTo(ProductBrandModel::class);
    }
}
