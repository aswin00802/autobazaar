<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'shop_orders';

    protected $guarded = [];

    protected $casts = [
        'payment_response' => 'array',
        'paid_at' => 'datetime',
    ];

    /** The five stages shown on the order tracking timeline. */
    public const FLOW = ['placed', 'confirmed', 'packed', 'shipped', 'delivered'];

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function history()
    {
        return $this->hasMany(OrderStatusHistory::class, 'order_id')->orderBy('id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getItemCountAttribute(): int
    {
        return (int) $this->items->sum('qty');
    }

    /** Position in FLOW, used to mark the timeline done/current/pending. */
    public function getStageIndexAttribute(): int
    {
        $i = array_search($this->order_status, self::FLOW, true);
        return $i === false ? 0 : $i;
    }
}
