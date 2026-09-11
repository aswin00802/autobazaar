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
        Schema::create('ride_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->double('pickup_lat', 10, 7);
            $table->double('pickup_lng', 10, 7);
            $table->double('drop_lat', 10, 7)->nullable();
            $table->double('drop_lng', 10, 7)->nullable();
            $table->string('pickup')->nullable();
            $table->string('drop')->nullable();
            $table->decimal('fare',8,2)->nullable();
            $table->decimal('distance',8,2)->nullable();
            $table->integer('duration')->nullable();
            $table->date('date')->nullable();
            $table->decimal('base_fare',8,2)->nullable();
            $table->decimal('per_km_rate',8,2)->nullable();
            $table->decimal('per_min_rate',8,2)->nullable();
            $table->decimal('waiting_rate',8,2)->nullable();
            $table->integer('waiting_mins')->nullable();
            $table->enum('status',[
                'pending',
                'accepted',
                'arrived',
                'started',
                'completed',
                'cancelled',
                'rejected'
            ])->default('pending');
            $table->timestamps();

            // Indexes
            $table->index('customer_id');
            $table->index('driver_id');
            $table->index('status');
            $table->index(['customer_id','status']);
            $table->index(['driver_id','status']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_requests');
    }
};
