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
        // Skips a table that is already there: the live database was built
        // from a dump, so many tables exist without this ever having run.
        Schema::hasTable('trips') || Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            // The column only. This migration is dated before create_targets_table,
            // so constraining it here fails on a fresh database — the foreign key
            // is added at the end instead, once every table exists.
            $table->unsignedBigInteger('target_id');
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
