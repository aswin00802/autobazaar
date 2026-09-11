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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('spareparts_categories')->onDelete('cascade');
            $table->foreignId('subcategory_id')->constrained('spareparts_sub_categories')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('image')->nullable();
            $table->string('sku')->unique()->nullable();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->integer('base_price')->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->string('images')->nullable();
            $table->integer('status_id')->default(1);
            $table->integer('created_by');
            $table->timestamps();
            $table->string('ip_address')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
