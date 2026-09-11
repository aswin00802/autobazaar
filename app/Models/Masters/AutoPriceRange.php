<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoPriceRange extends Model
{
    use HasFactory;
    protected $table = 'auto_price_expectations';
    protected $fillable = [];
}