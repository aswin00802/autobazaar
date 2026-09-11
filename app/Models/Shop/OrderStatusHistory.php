<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    protected $table = 'shop_order_status_histories';

    protected $fillable = ['order_id', 'order_status', 'note', 'created_by', 'ip_address'];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
