<?php

namespace App\Models\spareparts;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'subcategory_id',
        'name',
        'image',
        'sku',
        'description',
        'specifications',
        'base_price',
        'stock_quantity',
        'images',
        'created_by',
        'status_id', 
        'ip_address'
    ];

    public function Category()
    {
        return $this->belongsTo(SparepartsCategories::class,'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(SparepartsSubCategories::class,'subcategory_id');
    }

    public function productBrandModel(){
        return $this->hasMany(ProductBrandModel::class);
    }

    public function sparepartOrders()
    {
        return $this->hasMany(SparepartOrder::class, 'product_id');
    }
}
