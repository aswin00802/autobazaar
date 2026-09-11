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
        Schema::table('auto_emergency_services', function (Blueprint $table) {
            $table->string('vehicle_status')->nullable()->after('emergency_type');
            $table->string('vehicle_number')->nullable()->after('vehicle_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_emergency_services', function (Blueprint $table) {
            $table->dropColumn(['vehicle_status','vehicle_number']);
        });
    }
};
