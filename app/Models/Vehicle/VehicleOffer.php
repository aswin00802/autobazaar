<?php

namespace App\Models\Vehicle;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class VehicleOffer extends Model
{
    protected $table = 'vehicle_offers';
    protected $guarded = [];
    protected $casts = ['valid_from' => 'date', 'valid_to' => 'date', 'value_amount' => 'float'];

    /** Active today: status on and inside the validity window (open ends allowed). */
    public function scopeCurrent(Builder $q): Builder
    {
        $today = now()->toDateString();

        return $q->where('status_id', 1)
            ->where(fn ($w) => $w->whereNull('valid_from')->orWhereDate('valid_from', '<=', $today))
            ->where(fn ($w) => $w->whereNull('valid_to')->orWhereDate('valid_to', '>=', $today));
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }
}
