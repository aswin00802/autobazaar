<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_emergency_services` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_emergency_services')) {
            return;
        }

        Schema::create('auto_emergency_services', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->enum('emergency_type', ['accident', 'breakdown']);
            $table->string('vehicle_status', 255)->nullable();
            $table->string('vehicle_number', 255)->nullable();
            $table->longText('description')->nullable();
            $table->text('location')->nullable();
            $table->string('land_mark', 255)->nullable();
            $table->string('status', 30)->nullable();
            $table->timestamp('created_at', 6)->nullable()->useCurrent();
            $table->timestamp('updated_at', 6)->nullable()->useCurrent();

            $table->index(['status'], 'perf_auto_emergency_services_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_emergency_services');
    }
};
