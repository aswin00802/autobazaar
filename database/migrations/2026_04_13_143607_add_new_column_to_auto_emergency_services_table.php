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
        if (Schema::hasColumn('auto_emergency_services', 'land_mark')) {
            return;
        }

        Schema::table('auto_emergency_services', function (Blueprint $table) {
            $table->string('land_mark')->nullable()->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_emergency_services', function (Blueprint $table) {
            $table->dropColumn(['land_mark']);
        });
    }
};
