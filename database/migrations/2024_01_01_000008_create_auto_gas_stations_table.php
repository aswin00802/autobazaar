<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_gas_stations` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_gas_stations')) {
            return;
        }

        Schema::create('auto_gas_stations', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('fuel_id', 100)->nullable();
            $table->string('name', 100)->nullable();
            $table->string('location', 200)->nullable();
            $table->string('address', 200)->nullable();
            $table->string('map_link', 200)->nullable();
            $table->integer('status_id')->default(1);
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_gas_stations');
    }
};
