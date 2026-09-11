<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE ride_requests MODIFY COLUMN status ENUM(
            'scheduled',
            'pending',
            'accepted',
            'arrived',
            'started',
            'completed',
            'cancelled',
            'rejected'
        ) NOT NULL DEFAULT 'pending'");

        Schema::table('ride_requests', function (Blueprint $table) {
            $table->string('payment_status')->default('not_required')->after('estimated_fare');
            $table->decimal('advance_amount', 10, 2)->nullable()->after('payment_status');
            $table->string('razorpay_order_id')->nullable()->after('advance_amount');
            $table->string('razorpay_payment_id')->nullable()->after('razorpay_order_id');
            $table->string('razorpay_refund_id')->nullable()->after('razorpay_payment_id');
            $table->timestamp('paid_at')->nullable()->after('razorpay_refund_id');
            $table->timestamp('dispatched_at')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ride_requests', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'advance_amount',
                'razorpay_order_id',
                'razorpay_payment_id',
                'razorpay_refund_id',
                'paid_at',
                'dispatched_at',
            ]);
        });

        DB::statement("ALTER TABLE ride_requests MODIFY COLUMN status ENUM(
            'pending',
            'accepted',
            'arrived',
            'started',
            'completed',
            'cancelled',
            'rejected'
        ) NOT NULL DEFAULT 'pending'");
    }
};
