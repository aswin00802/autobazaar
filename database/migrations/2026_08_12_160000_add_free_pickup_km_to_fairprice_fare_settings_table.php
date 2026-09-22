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
        // The base schema already has this, so on a fresh install there is
        // nothing left to add here. On an older database the guard is false
        // and this runs exactly as it always did.
        if (Schema::hasColumn('fairprice_fare_settings', 'free_pickup_km')) {
            return;
        }

        Schema::table('fairprice_fare_settings', function (Blueprint $table) {
            $table->decimal('free_pickup_km', 8, 2)->default(2)->after('pickup_per_km_rate');
        });

        DB::table('fairprice_fare_settings')->update([
            'free_pickup_km' => 2,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fairprice_fare_settings', function (Blueprint $table) {
            $table->dropColumn('free_pickup_km');
        });
    }
};
