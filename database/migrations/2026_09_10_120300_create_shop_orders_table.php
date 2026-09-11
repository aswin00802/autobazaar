<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Accessory orders.
 *
 * Replaces the legacy `sparepart_orders` shape (one row per product, no
 * price, no payment call) with a proper order + line-items structure.
 * The legacy table is left in place for its 11 historic rows.
 *
 * Address and product details are SNAPSHOTTED onto the order: an order must
 * still read correctly years later even if the address book row is deleted or
 * the product is renamed or repriced.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 40)->unique();
            $table->integer('user_id')->index();

            // Money
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('coupon_code', 50)->nullable();
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('currency', 10)->default('INR');

            // Delivery choice
            $table->string('delivery_option', 30)->default('standard'); // standard | express

            // Address snapshot
            $table->string('shipping_name');
            $table->string('shipping_mobile', 20);
            $table->string('shipping_address_line_1');
            $table->string('shipping_address_line_2')->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_district', 100)->nullable();
            $table->string('shipping_state', 100)->nullable();
            $table->string('shipping_pincode', 10)->nullable();

            // Payment — same column names as sparepart_orders so the existing
            // Razorpay service can be reused without remapping.
            $table->string('payment_gateway')->nullable();
            $table->string('payment_mode')->nullable();             // upi | card | netbanking | wallet | cod
            $table->string('payment_status', 30)->default('pending');
            $table->string('payment_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('gateway_signature')->nullable();
            $table->json('payment_response')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->string('order_status', 30)->default('placed');  // placed | confirmed | packed | shipped | delivered | cancelled
            $table->text('notes')->nullable();

            $table->integer('status_id')->default(1);
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });

        Schema::create('shop_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('product_model_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();

            // Snapshot of what was bought
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->string('brand_name', 100)->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('unit_mrp', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2)->default(0);

            $table->integer('status_id')->default(1);
            $table->timestamps();
        });

        // Drives the tracking timeline on the order page.
        Schema::create('shop_order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->string('order_status', 30);
            $table->string('note')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_order_status_histories');
        Schema::dropIfExists('shop_order_items');
        Schema::dropIfExists('shop_orders');
    }
};
