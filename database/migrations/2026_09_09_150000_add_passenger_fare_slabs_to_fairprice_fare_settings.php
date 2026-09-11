<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fairprice_fare_settings', function (Blueprint $table) {
            $table->decimal('passenger_1_base_fare', 10, 2)->default(50)->after('base_km');
            $table->decimal('passenger_1_per_km', 10, 2)->default(10)->after('passenger_1_base_fare');
            $table->decimal('passenger_2_base_fare', 10, 2)->default(60)->after('passenger_1_per_km');
            $table->decimal('passenger_2_per_km', 10, 2)->default(20)->after('passenger_2_base_fare');
            $table->decimal('passenger_3_base_fare', 10, 2)->default(70)->after('passenger_2_per_km');
            $table->decimal('passenger_3_per_km', 10, 2)->default(30)->after('passenger_3_base_fare');
        });

        // Sync active row defaults (keeps existing base_fare/base_km)
        DB::table('fairprice_fare_settings')->where('is_active', 1)->update([
            'passenger_1_base_fare' => 50,
            'passenger_1_per_km' => 10,
            'passenger_2_base_fare' => 60,
            'passenger_2_per_km' => 20,
            'passenger_3_base_fare' => 70,
            'passenger_3_per_km' => 30,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('fairprice_fare_settings', function (Blueprint $table) {
            $table->dropColumn([
                'passenger_1_base_fare',
                'passenger_1_per_km',
                'passenger_2_base_fare',
                'passenger_2_per_km',
                'passenger_3_base_fare',
                'passenger_3_per_km',
            ]);
        });
    }
};
