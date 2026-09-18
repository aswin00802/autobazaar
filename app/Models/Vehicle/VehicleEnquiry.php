<?php

namespace App\Models\Vehicle;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Every lead the vehicle page produces, in one pipeline.
 */
class VehicleEnquiry extends Model
{
    protected $table = 'vehicle_enquiries';
    protected $guarded = [];
    protected $casts = ['buying_options' => 'array', 'preferred_at' => 'datetime', 'otp_verified' => 'boolean'];

    public const SOURCES = [
        'enquiry' => 'Enquiry',
        'quotation' => 'Quotation',
        'test_drive' => 'Test Drive',
        'loan' => 'Loan',
        'call' => 'Call',
        'whatsapp' => 'WhatsApp',
    ];

    /** Lead pipeline, in order (from the client's work plan). */
    public const PIPELINE = ['new', 'contacted', 'test_drive', 'documents', 'loan', 'booked', 'delivered', 'closed'];

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function variant()
    {
        return $this->belongsTo(VehicleVariant::class, 'vehicle_variant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
