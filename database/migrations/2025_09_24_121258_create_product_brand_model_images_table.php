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
        // Skips a table that is already there: the live database was built
        // from a dump, so many tables exist without this ever having run.
        Schema::hasTable('product_brand_model_images') || Schema::create('product_brand_model_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_brand_model_id')
                ->constrained('product_brand_models')
                ->onDelete('cascade');
            $table->string('image')->nullable(); // store path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_brand_model_images');
    }
};
