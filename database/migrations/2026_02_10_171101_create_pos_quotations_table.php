<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pos_quotations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('auto_id')->nullable();

            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile', 20)->nullable();
            $table->text('address')->nullable();

            // Amount fields
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('loan_percentage', 5, 2)->default(0);
            $table->decimal('total_loan_amount', 10, 2)->default(0);
            $table->decimal('interest', 5, 2)->default(0);

            $table->unsignedInteger('emi_months')->nullable();
            $table->decimal('emi_amount', 10, 2)->default(0);

            $table->decimal('down_payment', 10, 2)->default(0);
            $table->decimal('fitting_fee', 10, 2)->default(0);
            $table->decimal('permit_fee', 10, 2)->default(0);
            $table->decimal('loan_process_fee', 10, 2)->default(0);

            $table->unsignedTinyInteger('status_id')->default(1);
            $table->unsignedBigInteger('created_by');

            $table->ipAddress('ip_address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_quotations');
    }
};
