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
        if (Schema::hasColumn('pos_quotations', 'gifts')) {
            return;
        }

        Schema::table('pos_quotations', function (Blueprint $table) {
            $table->string('gifts')->nullable()->after('loan_process_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pos_quotations', function (Blueprint $table) {
            $table->dropColumn('gifts');
        });
    }
};
