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
        Schema::hasTable('targets') || Schema::create('targets', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->decimal('target_amount',10,2);
            $table->date('date')->index();
            $table->decimal('meter_amount',10,2)->default(0);
            $table->decimal('earning_amount',10,2)->default(0);
            $table->decimal('total_amount',10,2)->default(0);
            $table->boolean('is_completed')->default(false);
            $table->integer('status_id')->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'date']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('targets');
    }
};
