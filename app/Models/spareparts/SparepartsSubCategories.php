<?php

namespace App\Models\spareparts;

use Illuminate\Database\Eloquent\Model;

class SparepartsSubCategories extends Model
{
    protected $fillable = ['category_id','name', 'image', 'description', 'slug','created_by','status_id', 'ip_address'];

    public function category()
    {
        return $this->belongsTo(SparepartsCategories::class,'category_id');
    }
}
