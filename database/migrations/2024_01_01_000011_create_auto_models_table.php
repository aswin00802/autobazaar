<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_models` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_models')) {
            return;
        }

        Schema::create('auto_models', function (Blueprint $table) {
            $table->id();
            $table->integer('brand_id');
            $table->string('model_name', 255);
            $table->string('slug', 255)->nullable();
            $table->string('remark', 255)->nullable();
            $table->integer('status_id')->default(1);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('ip_address', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_models');
    }
};
