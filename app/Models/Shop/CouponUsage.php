<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $table = 'shop_coupon_usages';

    protected $fillable = ['coupon_id', 'user_id', 'order_id', 'discount_amount', 'ip_address'];
}
