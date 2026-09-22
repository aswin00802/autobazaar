<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `tbl_auto_areas` — read from the live database, which had no migration for it.
 *
 * Foreign keys are added afterwards, in the add-foreign-keys migration, so
 * the order these run in never matters.
 *
 * On a database that already has this table — the live one, or any copy of
 * it — this does nothing and is simply marked as run.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tbl_auto_areas')) {
            return;
        }

        Schema::create('tbl_auto_areas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name', 50);
            $table->bigInteger('city_id');
            $table->integer('status')->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_auto_areas');
    }
};
