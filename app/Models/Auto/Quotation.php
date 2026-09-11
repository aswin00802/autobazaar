<?php

namespace App\Models\Auto;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function auto()
    {
        return $this->belongsTo(Auto::class, 'post_id');
    }
}
