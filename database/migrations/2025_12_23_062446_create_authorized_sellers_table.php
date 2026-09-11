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
        Schema::create('authorized_sellers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('brand_id');
            $table->string('dealer_name')->nullable();
            $table->string('dealer_type')->nullable();
            $table->string('contact')->nullable();
            $table->string('location')->nullable();
            $table->text('address')->nullable();
            $table->string('distict')->nullable();
            $table->string('city')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorized_sellers');
    }
};
