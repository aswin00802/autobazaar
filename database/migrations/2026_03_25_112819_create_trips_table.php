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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->foreignId('target_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2); // trip amount
            $table->string('source')->nullable()->index(); // meter / extra / parcel / tips
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['target_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
