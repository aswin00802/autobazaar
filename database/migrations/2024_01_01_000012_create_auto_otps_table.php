<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_otps` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_otps')) {
            return;
        }

        Schema::create('auto_otps', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('phone', 15)->nullable();
            $table->integer('otp')->nullable();
            $table->string('status', 15)->nullable();
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();

            $table->index(['phone'], 'perf_auto_otps_phone_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_otps');
    }
};
