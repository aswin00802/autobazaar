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
        Schema::hasTable('spareparts_sub_categories') || Schema::create('spareparts_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('spareparts_categories')->cascadeOnDelete(); 
            $table->string('name');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->integer('status_id')->default(1);
            $table->integer('created_by');
            $table->timestamps();
            $table->string('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spareparts_sub_categories');
    }
};
