<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sos_alerts', function (Blueprint $table) {
            $table->unique(['ride_id', 'triggered_by'], 'sos_alerts_ride_trigger_unique');
        });
    }

    public function down(): void
    {
        Schema::table('sos_alerts', function (Blueprint $table) {
            $table->dropUnique('sos_alerts_ride_trigger_unique');
        });
    }
};
