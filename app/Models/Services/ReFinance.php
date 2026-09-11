<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Masters\AutoBrand;


class ReFinance extends Model
{
    use HasFactory;
    protected $table = 'auto_re_finance';
    protected $guarded = [];
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
    public function brand(){
        return $this->belongsTo(AutoBrand::class, 'brand_id','id');
    }
    
}
