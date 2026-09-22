<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The base schema already has this, so on a fresh install there is
        // nothing left to add here. On an older database the guard is false
        // and this runs exactly as it always did.
        if (Schema::hasColumn('customers', 'family_details')) {
            return;
        }

        Schema::table('customers', function (Blueprint $table) {
            $table->json('family_details')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('family_details');
        });
    }
};
