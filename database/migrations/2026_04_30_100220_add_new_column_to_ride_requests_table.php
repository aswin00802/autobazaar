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
        if (Schema::hasColumn('ride_requests', 'cancel_reason')) {
            return;
        }

        Schema::table('ride_requests', function (Blueprint $table) {
            $table->string('cancel_reason')->nullable()->after('cancelled_at');
            $table->string('cancelled_by')->nullable()->after('cancel_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropColumn(['cancel_reason','cancelled_by']);
        });
    }
};
