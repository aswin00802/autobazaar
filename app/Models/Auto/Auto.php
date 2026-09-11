<?php

namespace App\Models\Auto;

// use App\Models\Masters\AutoBodyTypes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Masters\AutoPriceRange;
use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoFuelType;
// use App\Models\Masters\AutoTransmission;
use App\Models\Masters\AutoOwners;
use App\Models\Enquiry;
use App\Models\Auto\Favourites;
use App\Models\User;
use App\Models\City;
use App\Models\Masters\AutoModel;

class Auto extends Model
{
    protected $table = 'auto_posts';

    protected $guarded = [];
    
    const USED_AUTO = 'used_auto';
    const NEW_AUTO = 'new_auto';

    // public function scopeNewAuto($all)
    // {
    //     return $all->where('auto_usage_status', self::NEW_AUTO);
    // }
    // public function scopeUsedAuto($all)
    // {
    //     return $all->where('auto_usage_status', self::USED_AUTO);
    // }

    public function scopeNewAuto($query)
    {
        return $query->where('auto_usage_status', self::NEW_AUTO);
    }
    public function scopeUsedAuto($query)
    {
        return $query->where('auto_usage_status', self::USED_AUTO);
    }

    public function autoBrands()
    {
        return $this->belongsTo(AutoBrand::class, 'auto_brand_id', 'id');
    }
    // new code
    public function autoModel() 
    {
        return $this->belongsTo(AutoModel::class, 'auto_model_id');
    }
    // end new code
  
    public function autoFueltype()
    {
        return $this->belongsTo(AutoFuelType::class, 'fuel_type_id', 'id');
    }

    public function autoPriceRange()
    {
        return $this->belongsTo(AutoPriceRange::class, 'price_expectations', 'id');
    }
    public function autoOwners()
    {
        return $this->belongsTo(AutoOwners::class, 'owner', 'id');
    }
    public function cities()
    {
        return $this->belongsTo(City::class, 'city', 'id');
    }
    public function getfavourites(){
        return $this->belongsTo(Favourites::class, 'id', 'post_id');

    }
    public function getEnquiry(){
        return $this->belongsTo(Enquiry::class, 'id', 'post_id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
   
}
