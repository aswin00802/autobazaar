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
        if (Schema::hasColumn('users', 'f_name')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('f_name')->nullable()->after('name');
            $table->string('l_name')->nullable()->after('f_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['f_name', 'l_name']);
        });
    }
};
