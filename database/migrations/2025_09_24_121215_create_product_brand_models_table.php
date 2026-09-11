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
        Schema::create('product_brand_models', function (Blueprint $table) {
             $table->id();
            // product_id
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();

            // brand_id → here match exactly your auto_brands.id (yours is BIGINT SIGNED)
            $table->bigInteger('brand_id'); 
            $table->foreign('brand_id')->references('id')->on('auto_brands')->cascadeOnDelete();

            // brand_model_id
            $table->unsignedBigInteger('brand_model_id');
            $table->foreign('brand_model_id')->references('id')->on('auto_models')->cascadeOnDelete();

            $table->decimal('price', 10, 2)->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('status_id')->default(1);
            $table->integer('created_by');
            $table->timestamps();
            $table->string('ip_address')->nullable();

            // $table->id(); 
            // $table->foreignId('product_id')->constrained()->cascadeOnDelete(); 
            // $table->foreignId('brand_id')->constrained('auto_brands')->cascadeOnDelete(); 
            // $table->foreignId('brand_model_id')->constrained('auto_models')->cascadeOnDelete(); 
            // $table->decimal('price', 10, 2)->nullable(); 
            // $table->decimal('offer_price', 10, 2)->nullable(); 
            // $table->boolean('is_available')->default(true); 
            // $table->integer('status_id')->default(1); 
            // $table->integer('created_by'); 
            // $table->timestamps(); 
            // $table->string('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_brand_models');
    }
};
