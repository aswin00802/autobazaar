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
