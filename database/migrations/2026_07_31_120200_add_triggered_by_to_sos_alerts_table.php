<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The base schema already has this, so on a fresh install there is
        // nothing left to add here. On an older database the guard is false
        // and this runs exactly as it always did.
        if (Schema::hasColumn('sos_alerts', 'triggered_by')) {
            return;
        }

        Schema::table('sos_alerts', function (Blueprint $table) {
            $table->string('triggered_by')->nullable()->after('ride_id')->comment('customer, driver');
            $table->unsignedBigInteger('triggered_by_id')->nullable()->after('triggered_by');
        });

        DB::table('sos_alerts')
            ->whereNull('triggered_by')
            ->update(['triggered_by' => 'customer']);
    }

    public function down(): void
    {
        Schema::table('sos_alerts', function (Blueprint $table) {
            $table->dropColumn(['triggered_by', 'triggered_by_id']);
        });
    }
};
