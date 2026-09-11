<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('ride_requests', 'start_pin')) {
                $table->string('start_pin', 4)->nullable()->after('accepted_at');
            }
            if (!Schema::hasColumn('ride_requests', 'start_pin_attempts')) {
                $table->unsignedTinyInteger('start_pin_attempts')->default(0)->after('start_pin');
            }
            if (!Schema::hasColumn('ride_requests', 'start_pin_verified_at')) {
                $table->timestamp('start_pin_verified_at')->nullable()->after('start_pin_attempts');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            foreach (['start_pin', 'start_pin_attempts', 'start_pin_verified_at'] as $col) {
                if (Schema::hasColumn('ride_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
