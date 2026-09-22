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
        if (Schema::hasColumn('spareparts_sub_categories', 'slug')) {
            return;
        }

        Schema::table('spareparts_sub_categories', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spareparts_sub_categories', function (Blueprint $table) {
            $table->dropColumn(['slug']);
        });
    }
};
