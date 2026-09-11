<?php

namespace App\Models\Shop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table = 'shop_addresses';

    protected $fillable = [
        'user_id', 'label', 'name', 'mobile',
        'address_line_1', 'address_line_2',
        'city', 'district', 'state', 'pincode',
        'is_default', 'status_id', 'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** ["12, Anna Nagar", "Chennai - 600040, Tamil Nadu"] */
    public function getLinesAttribute(): array
    {
        $tail = trim(collect([$this->city, $this->pincode ? '- ' . $this->pincode : null])
            ->filter()->implode(' '));

        return collect([
            $this->address_line_1,
            $this->address_line_2,
            trim($tail . ($this->state ? ', ' . $this->state : ''), ', '),
        ])->filter()->values()->all();
    }
}
