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
        if (Schema::hasIndex('users', 'users_latitude_longitude_index')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->index(['latitude','longitude']);
            $table->index('is_online');
            $table->index('is_available');

            // best performance
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['latitude','longitude']);
            $table->dropIndex(['is_online']);
            $table->dropIndex(['is_available']);
        });
    }
};
