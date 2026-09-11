<?php

namespace App\Models;

use App\Models\Masters\AutoAreas;
use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    public function location(){
        return $this->belongsTo(AutoAreas::class, 'location_id','id');
    }
}
