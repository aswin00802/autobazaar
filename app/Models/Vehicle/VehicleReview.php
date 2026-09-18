<?php

namespace App\Models\Vehicle;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class VehicleReview extends Model
{
    protected $table = 'vehicle_reviews';
    protected $guarded = [];
    protected $casts = ['is_verified' => 'boolean'];

    public const STATUSES = ['pending', 'approved', 'rejected'];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
