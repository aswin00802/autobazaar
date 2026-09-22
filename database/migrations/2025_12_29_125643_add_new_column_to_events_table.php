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
        // The base schema already has this, so on a fresh install there is
        // nothing left to add here. On an older database the guard is false
        // and this runs exactly as it always did.
        if (Schema::hasColumn('events', 'audio_file')) {
            return;
        }

        Schema::table('events', function (Blueprint $table) {
            $table->string('audio_file')->nullable()->after('description');
            $table->text('map_link')->nullable()->after('audio_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('audio_file');
            $table->dropColumn('map_link');
        });
    }
};
