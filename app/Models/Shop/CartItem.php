<?php

namespace App\Models\Shop;

use App\Models\spareparts\ProductBrandModel;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'shop_cart_items';

    protected $fillable = [
        'cart_id',
        'product_model_id',
        'qty',
        'unit_price',
        'unit_mrp',
        'status_id',
        'ip_address',
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class, 'cart_id');
    }

    /** The priced, purchasable variant — same target as sparepart_orders.product_id. */
    public function productModel()
    {
        return $this->belongsTo(ProductBrandModel::class, 'product_model_id');
    }

    public function getLineTotalAttribute(): float
    {
        return (float) $this->unit_price * (int) $this->qty;
    }
}
