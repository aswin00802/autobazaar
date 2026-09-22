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
        if (Schema::hasColumn('ride_requests', 'accepted_at')) {
            return;
        }

        Schema::table('ride_requests', function (Blueprint $table) {
            $table->timestamp('accepted_at')->nullable()->after('status');
            $table->timestamp('started_at')->nullable()->after('accepted_at');
            $table->timestamp('completed_at')->nullable()->after('started_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropColumn([
                'accepted_at',
                'started_at',
                'completed_at',
                'cancelled_at'
            ]);
        });
    }
};
