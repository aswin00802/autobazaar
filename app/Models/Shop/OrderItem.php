<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'shop_order_items';

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
