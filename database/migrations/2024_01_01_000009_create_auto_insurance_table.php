<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_insurance` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_insurance')) {
            return;
        }

        Schema::create('auto_insurance', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->bigInteger('brand_id');
            $table->string('specific_model', 50)->nullable();
            $table->string('registration_year', 25)->nullable();
            $table->string('registration_number', 50)->nullable();
            $table->enum('status', ['live', 'expired'])->nullable();
            $table->timestamp('created_at', 6)->nullable();
            $table->timestamp('updated_at', 6)->nullable();

            $table->index(['user_id'], 'user_id');
            $table->index(['brand_id'], 'brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_insurance');
    }
};
