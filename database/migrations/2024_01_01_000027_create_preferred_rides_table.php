<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `preferred_rides` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('preferred_rides')) {
            return;
        }

        Schema::create('preferred_rides', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('pickup', 255)->nullable();
            $table->string('drop_in', 255)->nullable();
            $table->string('pickup_city', 255)->nullable();
            $table->string('dropin_city', 255)->nullable();
            $table->string('passengers_count', 255)->nullable();
            $table->string('from_date', 255)->nullable();
            $table->string('time', 255)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index(['user_id'], 'preferred_rides_user_id_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preferred_rides');
    }
};
