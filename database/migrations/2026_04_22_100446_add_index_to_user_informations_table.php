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
        if (Schema::hasIndex('user_informations', 'user_informations_user_id_index')) {
            return;
        }

        Schema::table('user_informations', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('fuel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_informations', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['fuel_id']);
        });
    }
};
