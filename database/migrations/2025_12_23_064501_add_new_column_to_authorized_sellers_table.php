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
        if (Schema::hasColumn('authorized_sellers', 'status_id')) {
            return;
        }

        Schema::table('authorized_sellers', function (Blueprint $table) {
            $table->integer('status_id')->default(1)->after('image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authorized_sellers', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
};
