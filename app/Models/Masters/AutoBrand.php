<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoBrand extends Model
{
    use HasFactory;
    protected $table = 'auto_brands';
    protected $guarded = [];

    public function autoModel(){
        return $this->hasMany(AutoModel::class,'brand_id');
    }
}
