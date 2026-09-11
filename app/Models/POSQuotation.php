<?php

namespace App\Models;

use App\Models\Auto\Auto;
use Illuminate\Database\Eloquent\Model;

class POSQuotation extends Model
{
    protected $table = 'pos_quotations';
    protected $fillable = [
        'auto_id',
        'quotation_no',
        'name',
        'email',
        'mobile',
        'address',
        'discount_amount',
        'total_amount',
        'loan_percentage',
        'total_loan_amount',
        'interest',
        'emi_months',
        'emi_amount',
        'down_payment',
        'fitting_fee',
        'permit_fee',
        'loan_process_fee',
        'gifts',
        'status_id',
        'created_by',
        'ip_address',
    ];

    protected $casts = [
        'gifts' => 'array',
    ];

    public function auto(){
        return $this->belongsTo(Auto::class, 'auto_id','id');
    }
}
