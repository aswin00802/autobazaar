<?php

namespace App\Models;

use App\Models\Services\Finance;
use Illuminate\Database\Eloquent\Model;

/**
 * Loan terms for one finance partner (auto_financiar row).
 */
class FinanceLenderRate extends Model
{
    protected $table = 'finance_lender_rates';
    protected $guarded = [];
    protected $casts = ['interest_rate' => 'float', 'processing_fee_pct' => 'float', 'is_featured' => 'boolean'];

    public function lender()
    {
        return $this->belongsTo(Finance::class, 'auto_financiar_id');
    }

    /** Tenures offered, in 12-month steps. */
    public function tenures(): array
    {
        $out = [];
        for ($m = (int) $this->min_tenure_months; $m <= (int) $this->max_tenure_months; $m += 12) {
            $out[] = $m;
        }

        return $out ?: [36];
    }
}
