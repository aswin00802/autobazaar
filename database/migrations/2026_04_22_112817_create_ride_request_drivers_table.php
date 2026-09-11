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
        Schema::create('ride_request_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('ride_requests')->cascadeOnDelete();
            $table->integer('driver_id');
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected',
                'expired'
            ])->default('pending');
            $table->timestamps();

            $table->index('driver_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ride_request_drivers');
    }
};
