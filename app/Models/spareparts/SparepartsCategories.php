<?php

namespace App\Models\spareparts;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class SparepartsCategories extends Model
{
    protected $fillable = ['name', 'image', 'description', 'slug','created_by','status_id', 'ip_address'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    public function subcategories(){
        return $this->hasMany(SparepartsSubCategories::class,'category_id','id');
    }
}
