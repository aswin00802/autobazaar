<?php

namespace App\Models\spareparts;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SparepartOrder extends Model
{
    protected $table = 'sparepart_orders';

    protected $fillable = [
        'user_id',
        'product_id',
        'qnty',
        'order_id',
        'payment_gateway',
        'payment_mode',
        'payment_amount',
        'payment_status',
        'payment_id',
        'transaction_id',
        'currency',
        'gateway_signature',
        'payment_response',
        'billing_name',
        'billing_email',
        'billing_mobile',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_country',
        'billing_zipcode',
        'order_status',
        'stauts_id',
        'ip_address',
    ];

    // 🔹 Relation with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 🔹 Relation with Product
    public function product()
    {
        return $this->belongsTo(ProductBrandModel::class, 'product_id');
    }
}
