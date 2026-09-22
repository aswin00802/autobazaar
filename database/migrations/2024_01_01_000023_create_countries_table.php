<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `countries` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('countries')) {
            return;
        }

        Schema::create('countries', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('shortname', 3);
            $table->string('name', 150);
            $table->integer('phonecode');
            $table->string('symbol', 255)->nullable();
            $table->string('currency_name', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
