<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Auto\Auto;

class Enquiry extends Model
{
    use HasFactory;
    protected $table = 'auto_enquiries';
    protected $guarded = [];

    public function getAutoPost(){
        return $this->belongsTo(Auto::class, 'post_id', 'id'); 
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id'); 
    }
    public function auto(){
        return $this->belongsTo(Auto::class, 'post_id', 'id'); 
    }

}
