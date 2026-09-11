<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'shop_carts';

    protected $fillable = [
        'user_id',
        'session_id',
        'coupon_code',
        'cart_status',
        'status_id',
        'ip_address',
    ];

    public function items()
    {
        return $this->hasMany(CartItem::class, 'cart_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
