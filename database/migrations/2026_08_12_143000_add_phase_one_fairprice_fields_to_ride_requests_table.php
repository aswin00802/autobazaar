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
        if (Schema::hasColumn('ride_requests', 'booking_type')) {
            return;
        }

        Schema::table('ride_requests', function (Blueprint $table) {
            $table->string('booking_type')->default('instant')->after('passenger_count');
            $table->dateTime('scheduled_at')->nullable()->after('booking_type');
            $table->string('other_phone', 10)->nullable()->after('scheduled_at');

            $table->decimal('trip_distance_km', 8, 2)->nullable()->after('other_phone');
            $table->decimal('pickup_distance_km', 8, 2)->nullable()->after('trip_distance_km');

            $table->decimal('trip_fare', 10, 2)->nullable()->after('pickup_distance_km');
            $table->decimal('pickup_fare', 10, 2)->nullable()->after('trip_fare');
            $table->decimal('waiting_fare', 10, 2)->default(0)->after('pickup_fare');
            $table->decimal('estimated_fare', 10, 2)->nullable()->after('waiting_fare');

            $table->timestamp('waiting_started_at')->nullable()->after('estimated_fare');
            $table->timestamp('waiting_ended_at')->nullable()->after('waiting_started_at');
            $table->integer('waiting_chargeable_mins')->nullable()->after('waiting_ended_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropColumn([
                'booking_type',
                'scheduled_at',
                'other_phone',
                'trip_distance_km',
                'pickup_distance_km',
                'trip_fare',
                'pickup_fare',
                'waiting_fare',
                'estimated_fare',
                'waiting_started_at',
                'waiting_ended_at',
                'waiting_chargeable_mins',
            ]);
        });
    }
};
