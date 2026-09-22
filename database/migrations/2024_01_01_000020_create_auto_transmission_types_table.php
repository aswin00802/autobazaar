<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_transmission_types` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_transmission_types')) {
            return;
        }

        Schema::create('auto_transmission_types', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 250);
            $table->integer('status')->nullable()->default(1);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_transmission_types');
    }
};
