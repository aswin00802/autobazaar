<?php 

namespace App\Models;

use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoFuelType;
use App\Models\Masters\AutoModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserInformation extends Model
{
    use HasFactory;

    protected $table = 'user_informations';
    // protected $fillable = ['user_id', 'profile','brand_id','model_id','fuel_id','vehicle_no','millage'];
    protected $fillable = [
        'user_id',

        // Profile
        'profile',
        'dob',
        'gender',
        'blood_group',
        'address',

        // Vehicle
        'brand_id',
        'model_id',
        'fuel_id',
        'vehicle_no',
        'millage',
        'seating_capacity',

        // Driving License
        'driving_license_no',
        'driving_license_image',
        'driving_license_expiry',

        // RC Book
        'rc_book_no',
        'rc_book_image',

        // Insurance
        'insurance_expiry',
        'insurance_image',

        // Permit
        'permit_no',
        'permit_expiry',

        // Fitness
        'fitness_expiry',

        // Police Verification
        'police_verification',

        // Aadhaar
        'aadhaar_no',
        'aadhaar_copy',
    ];

    protected $casts = [
        'dob'                    => 'date',
        'driving_license_expiry' => 'date',
        'insurance_expiry'       => 'date',
        'permit_expiry'          => 'date',
        'fitness_expiry'         => 'date',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function autoBrand()
    {
        return $this->belongsTo(AutoBrand::class, 'brand_id', 'id');
    }

    public function autoModel() 
    {
        return $this->belongsTo(AutoModel::class, 'model_id');
    }
  
    public function autoFueltype()
    {
        return $this->belongsTo(AutoFuelType::class, 'fuel_id', 'id');
    }
}
