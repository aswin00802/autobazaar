<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The base schema already has this, so on a fresh install there is
        // nothing left to add here. On an older database the guard is false
        // and this runs exactly as it always did.
        if (Schema::hasColumn('ride_requests', 'purpose')) {
            return;
        }

        Schema::table('ride_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('ride_requests', 'purpose')) {
                $table->string('purpose', 50)->nullable()->after('hire_type');
            }
            if (!Schema::hasColumn('ride_requests', 'pickup_at')) {
                $table->dateTime('pickup_at')->nullable()->after('scheduled_at');
            }
            if (!Schema::hasColumn('ride_requests', 'drop_at')) {
                $table->dateTime('drop_at')->nullable()->after('pickup_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            foreach (['purpose', 'pickup_at', 'drop_at'] as $col) {
                if (Schema::hasColumn('ride_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
