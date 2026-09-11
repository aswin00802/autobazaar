<?php

namespace App\Models\spareparts;

use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoModel;
use Illuminate\Database\Eloquent\Model;

class ProductBrandModel extends Model
{
    protected $fillable = [
        'product_id',
        'brand_id',
        'brand_model_id',
        'price',
        'offer_price',
        'is_available',
        'created_by',
        'status_id', 
        'ip_address'
    ];

    public function autoBrands()
    {
        return $this->belongsTo(AutoBrand::class, 'brand_id');
    }
    // new code
    public function autoModel() 
    {
        return $this->belongsTo(AutoModel::class, 'brand_model_id');
    }

    public function product() 
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function images()
    {
        return $this->hasMany(ProductBrandModelImage::class);
    }

}
