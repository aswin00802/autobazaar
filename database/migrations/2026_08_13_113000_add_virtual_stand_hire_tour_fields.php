<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('ride_requests', 'target_driver_id')) {
                $table->unsignedBigInteger('target_driver_id')->nullable()->after('driver_id');
            }
            if (!Schema::hasColumn('ride_requests', 'hire_type')) {
                $table->string('hire_type', 20)->nullable()->after('booking_type');
            }
            if (!Schema::hasColumn('ride_requests', 'booking_comment')) {
                $table->text('booking_comment')->nullable()->after('hire_type');
            }
            if (!Schema::hasColumn('ride_requests', 'is_round_trip')) {
                $table->boolean('is_round_trip')->default(false)->after('booking_comment');
            }
            if (!Schema::hasColumn('ride_requests', 'refund_deduction_percent')) {
                $table->decimal('refund_deduction_percent', 5, 2)->nullable()->after('razorpay_refund_id');
            }
            if (!Schema::hasColumn('ride_requests', 'refund_amount')) {
                $table->decimal('refund_amount', 10, 2)->nullable()->after('refund_deduction_percent');
            }
        });

        Schema::table('fairprice_fare_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('fairprice_fare_settings', 'tour_per_km_rate')) {
                $table->decimal('tour_per_km_rate', 10, 2)->default(18)->after('waiting_per_min_rate');
            }
            if (!Schema::hasColumn('fairprice_fare_settings', 'tour_min_km')) {
                $table->decimal('tour_min_km', 8, 2)->default(200)->after('tour_per_km_rate');
            }
            if (!Schema::hasColumn('fairprice_fare_settings', 'hire_daily_per_km_rate')) {
                $table->decimal('hire_daily_per_km_rate', 10, 2)->default(18)->after('tour_min_km');
            }
            if (!Schema::hasColumn('fairprice_fare_settings', 'hire_monthly_per_km_rate')) {
                $table->decimal('hire_monthly_per_km_rate', 10, 2)->default(15)->after('hire_daily_per_km_rate');
            }
            if (!Schema::hasColumn('fairprice_fare_settings', 'hire_tour_advance_percent')) {
                $table->unsignedTinyInteger('hire_tour_advance_percent')->default(50)->after('hire_monthly_per_km_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $cols = [
                'target_driver_id',
                'hire_type',
                'booking_comment',
                'is_round_trip',
                'refund_deduction_percent',
                'refund_amount',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('ride_requests', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('fairprice_fare_settings', function (Blueprint $table) {
            $cols = [
                'tour_per_km_rate',
                'tour_min_km',
                'hire_daily_per_km_rate',
                'hire_monthly_per_km_rate',
                'hire_tour_advance_percent',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('fairprice_fare_settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
