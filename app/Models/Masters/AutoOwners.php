<?php

namespace App\Models\Masters;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoOwners extends Model
{
    use HasFactory;
    protected $table = 'auto_owners';
    protected $guarded = [];
}
