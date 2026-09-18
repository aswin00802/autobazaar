<?php

namespace App\Models\Vehicle;

use App\Models\Masters\AuthorizedSeller;
use App\Models\Masters\AutoBrand;
use App\Models\Masters\AutoModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A marketed autorickshaw model (TVS King Deluxe). Everything the detail
 * page shows hangs off this row. `auto_posts` remains the used-auto table.
 */
class VehicleModel extends Model
{
    protected $table = 'vehicle_models';

    protected $guarded = [];

    protected $casts = [
        'use_case' => 'array',
        'is_popular' => 'boolean',
        'rating_avg' => 'float',
        'score_overall' => 'float',
    ];

    /** Live rows only (status_id 1). Drafts stay visible in admin. */
    public function scopeLive(Builder $q): Builder
    {
        return $q->where('status_id', 1);
    }

    public function brand()
    {
        return $this->belongsTo(AutoBrand::class, 'auto_brand_id');
    }

    public function masterModel()
    {
        return $this->belongsTo(AutoModel::class, 'auto_model_id');
    }

    public function dealer()
    {
        return $this->belongsTo(AuthorizedSeller::class, 'dealer_id');
    }

    public function variants()
    {
        return $this->hasMany(VehicleVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    public function specifications()
    {
        return $this->hasMany(VehicleSpecification::class)->orderBy('sort_order')->orderBy('id');
    }

    public function images()
    {
        return $this->hasMany(VehicleImage::class)->where('status_id', 1)->orderBy('sort_order')->orderBy('id');
    }

    public function prices()
    {
        return $this->hasMany(VehiclePrice::class)->where('status_id', 1);
    }

    public function scores()
    {
        return $this->hasMany(VehicleScore::class)->orderBy('sort_order')->orderBy('id');
    }

    public function features()
    {
        return $this->hasMany(VehicleFeature::class)->orderBy('sort_order')->orderBy('id');
    }

    public function suitability()
    {
        return $this->hasMany(VehicleSuitability::class)->orderBy('sort_order')->orderBy('id');
    }

    public function offers()
    {
        return $this->hasMany(VehicleOffer::class)->orderBy('sort_order')->orderBy('id');
    }

    public function documents()
    {
        return $this->hasMany(VehicleDocument::class)->where('status_id', 1)->orderBy('sort_order');
    }

    public function stock()
    {
        return $this->hasMany(VehicleStock::class)->where('status_id', 1);
    }

    public function reviews()
    {
        return $this->hasMany(VehicleReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(VehicleReview::class)->where('review_status', 'approved')->latest();
    }

    public function enquiries()
    {
        return $this->hasMany(VehicleEnquiry::class);
    }

    /** Recompute the cached rating from approved reviews. */
    public function refreshRating(): void
    {
        $q = $this->reviews()->where('review_status', 'approved');
        $this->forceFill([
            'rating_count' => (int) $q->count(),
            'rating_avg' => round((float) $q->avg('rating'), 1),
        ])->saveQuietly();
    }
}
