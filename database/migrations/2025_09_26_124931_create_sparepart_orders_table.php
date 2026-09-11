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
        Schema::create('sparepart_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('product_id');
            $table->integer('qnty')->default(0);
            $table->string('order_id')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('payment_amount')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('currency', 10)->default('INR');
            $table->string('gateway_signature')->nullable();
            $table->json('payment_response')->nullable();
            $table->string('billing_name')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_mobile')->nullable();
            $table->string('billing_address')->nullable();
            $table->string('billing_city')->nullable();
            $table->string('billing_state')->nullable();
            $table->string('billing_country')->nullable();
            $table->string('billing_zipcode')->nullable();
            $table->string('order_status')->default('pending');
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sparepart_orders');
    }
};
