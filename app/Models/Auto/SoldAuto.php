<?php

namespace App\Models\Auto;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoldAuto extends Model
{
    use HasFactory;
    protected $table = 'auto_solds';
    protected $guarded = [];

    public function auto()
    {
        return $this->belongsTo('App\Models\Auto\Auto', 'post_id');
    }
}