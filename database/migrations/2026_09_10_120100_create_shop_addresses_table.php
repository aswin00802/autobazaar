<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Customer address book (cart.jpeg / my_orders.jpeg).
 *
 * Stores plain text for state/district/city rather than FKs: the existing
 * `states` and `cities` tables have no district level, and delivery addresses
 * need to survive unchanged even if those master rows are edited later.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shop_addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index();
            $table->string('label', 50)->default('Home');       // Home | Office | Other
            $table->string('name');
            $table->string('mobile', 20);
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->integer('status_id')->default(1);
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shop_addresses');
    }
};
