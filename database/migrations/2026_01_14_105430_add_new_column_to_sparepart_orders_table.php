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
        if (Schema::hasColumn('sparepart_orders', 'stauts_id')) {
            return;
        }

        Schema::table('sparepart_orders', function (Blueprint $table) {
            $table->integer('stauts_id')->default(1)->after('order_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sparepart_orders', function (Blueprint $table) {
            $table->dropColumn(['stauts_id']);
        });
    }
};
