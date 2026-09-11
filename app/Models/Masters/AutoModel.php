<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Masters\AutoBrand;

class AutoModel extends Model
{
    use HasFactory;

    protected $table = 'auto_models';

    protected $fillable = [
        'brand_id',
        'model_name',
        'slug',
        'status_id',
        'remark',
        'ip_address',
    ];

    public function brand(){
        return $this->belongsTo(AutoBrand::class, 'brand_id', 'id');
        
    }
}
