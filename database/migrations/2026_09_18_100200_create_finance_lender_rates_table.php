<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Loan terms per finance partner, for the "Finance Options" card.
 *
 * Kept in its own table rather than altering `auto_financiar` (live data,
 * used by the mobile app). One row per partner; edited from the existing
 * Finance Partners admin screen.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Skips a table that is already there: the live database was built
        // from a dump, so many tables exist without this ever having run.
        Schema::hasTable('finance_lender_rates') || Schema::create('finance_lender_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('auto_financiar_id')->unique();       // auto_financiar.id
            $table->decimal('interest_rate', 5, 2)->default(11.50);          // % p.a.
            $table->tinyInteger('min_tenure_months')->default(12);
            $table->tinyInteger('max_tenure_months')->default(60);
            $table->tinyInteger('max_loan_pct')->default(85);                // with CIBIL
            $table->tinyInteger('max_loan_pct_no_cibil')->default(70);
            $table->decimal('processing_fee_pct', 4, 2)->default(0);
            $table->text('documents')->nullable();                           // one per line
            $table->tinyInteger('is_featured')->default(0);
            $table->integer('sort_order')->default(0);
            $table->integer('status_id')->default(1);
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_lender_rates');
    }
};
