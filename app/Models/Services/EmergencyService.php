<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;



class EmergencyService extends Model
{
    use HasFactory;
    protected $table = 'auto_emergency_services';
    protected $guarded = [];
    
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
   
}
