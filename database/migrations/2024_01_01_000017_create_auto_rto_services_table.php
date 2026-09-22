<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_rto_services` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_rto_services')) {
            return;
        }

        Schema::create('auto_rto_services', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->string('rto_number', 20)->nullable();
            $table->string('service_type', 50)->nullable();
            $table->string('status', 20);
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();

            $table->index(['user_id'], 'user');
            $table->index(['status'], 'perf_auto_rto_services_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_rto_services');
    }
};
