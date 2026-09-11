<?php

namespace App\Models\Masters;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuthorizedSeller extends Model
{
    public function brand(){
        return $this->belongsTo(AutoBrand::class, 'brand_id', 'id');
        
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
