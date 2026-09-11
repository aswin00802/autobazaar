<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Discount codes. AUTO5 (5% off a first order) is seeded to match cart.jpeg.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('label')->nullable();
            $table->string('discount_type', 20)->default('percent');  // percent | flat
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->decimal('min_order_amount', 10, 2)->default(0);
            $table->decimal('max_discount_amount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();               // null = unlimited
            $table->integer('per_user_limit')->default(1);
            $table->tinyInteger('first_order_only')->default(0);
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->integer('status_id')->default(1);
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });

        Schema::create('shop_coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id')->index();
            $table->integer('user_id')->index();
            $table->unsignedBigInteger('order_id')->nullable()->index();
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });

        // Seeded so the checkout screen works out of the box, as drawn.
        DB::table('shop_coupons')->insert([
            'code' => 'AUTO5',
            'label' => '5% off on your first order',
            'discount_type' => 'percent',
            'discount_value' => 5,
            'min_order_amount' => 0,
            'max_discount_amount' => null,
            'usage_limit' => null,
            'per_user_limit' => 1,
            'first_order_only' => 1,
            'valid_from' => null,
            'valid_to' => null,
            'status_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_coupon_usages');
        Schema::dropIfExists('shop_coupons');
    }
};
