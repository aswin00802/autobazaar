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
        // The base schema already has this, so on a fresh install there is
        // nothing left to add here. On an older database the guard is false
        // and this runs exactly as it always did.
        if (Schema::hasColumn('user_informations', 'brand_id')) {
            return;
        }

        Schema::table('user_informations', function (Blueprint $table) {
            $table->integer('brand_id')->nullable()->after('profile');
            $table->integer('model_id')->nullable()->after('brand_id');
            $table->integer('fuel_id')->nullable()->after('model_id');
            $table->string('vehicle_no')->nullable()->after('fuel_id');
            $table->string('millage')->nullable()->after('vehicle_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_informations', function (Blueprint $table) {
            $table->dropColumn(['brand_id','model_id','fuel_id','vehicle_no','millage']);
        });
    }
};
