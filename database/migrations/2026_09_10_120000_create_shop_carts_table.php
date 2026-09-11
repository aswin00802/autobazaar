<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Accessories cart.
 *
 * A cart belongs to EITHER a signed-in user or a guest session — never both.
 * On login the guest cart is merged into the user's and then closed, so a
 * visitor never loses what they picked before signing in.
 *
 * Separate from the legacy `sparepart_orders` flow, which writes one row per
 * product with no price and no payment. That table is left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_carts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable()->index();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('coupon_code', 50)->nullable();
            $table->string('cart_status', 20)->default('active');   // active | merged | converted
            $table->integer('status_id')->default(1);
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });

        Schema::create('shop_cart_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cart_id')->index();

            // Points at product_brand_models.id — the priced, purchasable
            // variant. Matches how sparepart_orders.product_id already works.
            $table->unsignedBigInteger('product_model_id')->index();

            $table->integer('qty')->default(1);

            // Price captured at add-to-cart time, so a later price change
            // does not silently rewrite what the customer saw.
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('unit_mrp', 10, 2)->default(0);

            $table->integer('status_id')->default(1);
            $table->timestamps();
            $table->string('ip_address')->nullable();

            $table->unique(['cart_id', 'product_model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_cart_items');
        Schema::dropIfExists('shop_carts');
    }
};
