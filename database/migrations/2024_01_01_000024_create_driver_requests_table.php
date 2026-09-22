<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `driver_requests` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('driver_requests')) {
            return;
        }

        Schema::create('driver_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name', 255);
            $table->string('email', 255)->nullable();
            $table->string('number', 20)->nullable();
            $table->integer('area_id')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('deleted_at')->nullable();

            $table->index(['status'], 'perf_driver_requests_status_idx');
            $table->index(['created_at'], 'perf_driver_requests_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_requests');
    }
};
