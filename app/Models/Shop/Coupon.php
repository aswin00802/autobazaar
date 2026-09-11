<?php

namespace App\Models\Shop;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $table = 'shop_coupons';

    protected $guarded = [];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class, 'coupon_id');
    }

    /** Discount this coupon yields on a given subtotal, capped if configured. */
    public function discountOn(float $subtotal): float
    {
        if ($subtotal < (float) $this->min_order_amount) {
            return 0;
        }

        $discount = $this->discount_type === 'percent'
            ? $subtotal * ((float) $this->discount_value / 100)
            : (float) $this->discount_value;

        if ($this->max_discount_amount !== null) {
            $discount = min($discount, (float) $this->max_discount_amount);
        }

        return (float) min(floor($discount), $subtotal);
    }
}
