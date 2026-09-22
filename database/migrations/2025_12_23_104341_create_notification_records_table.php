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
        Schema::hasTable('notification_records') || Schema::create('notification_records', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->longText('payload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_records');
    }
};
