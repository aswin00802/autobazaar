<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('fairprice_fare_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('trip_per_km_rate', 10, 2)->default(18);
            $table->decimal('pickup_per_km_rate', 10, 2)->default(18);
            $table->decimal('free_pickup_km', 8, 2)->default(2);
            $table->decimal('base_fare', 10, 2)->default(50);
            $table->decimal('base_km', 8, 2)->default(1.8);
            $table->decimal('min_billable_trip_km', 8, 2)->default(2);
            $table->unsignedInteger('waiting_free_mins')->default(5);
            $table->decimal('waiting_per_min_rate', 10, 2)->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('fairprice_fare_settings')->insert([
            'trip_per_km_rate' => 18,
            'pickup_per_km_rate' => 18,
            'free_pickup_km' => 2,
            'base_fare' => 50,
            'base_km' => 1.8,
            'min_billable_trip_km' => 2,
            'waiting_free_mins' => 5,
            'waiting_per_min_rate' => 2,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fairprice_fare_settings');
    }
};
