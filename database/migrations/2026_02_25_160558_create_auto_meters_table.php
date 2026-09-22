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
        Schema::hasTable('auto_meters') || Schema::create('auto_meters', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('invoice_no')->unique()->nullable();
            $table->string('invoice_inc_id')->unique()->nullable();
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->decimal('total_km', 8, 2)->nullable(); 
            $table->string('total_time')->nullable();
            $table->decimal('km_amount', 10, 2)->nullable();
            $table->decimal('fuel_amount', 10, 2)->nullable();
            $table->decimal('friction_amount', 10, 2)->nullable();
            $table->decimal('wages_amount', 10, 2)->nullable();
            $table->decimal('tips_amount', 10, 2)->nullable();
            $table->decimal('margin_amount', 10, 2)->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->date('date')->nullable();
            $table->integer('status_id')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_meters');
    }
};
