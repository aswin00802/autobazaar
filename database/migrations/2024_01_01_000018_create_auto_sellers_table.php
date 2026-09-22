<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_sellers` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_sellers')) {
            return;
        }

        Schema::create('auto_sellers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('seller_name', 50)->nullable();
            $table->string('seller_type', 50)->nullable();
            $table->string('location', 150)->nullable();
            $table->string('address', 200)->nullable();
            $table->bigInteger('contact')->nullable();
            $table->integer('status_id')->default(1);
            $table->timestamp('created_at', 6)->nullable()->useCurrent();
            $table->timestamp('updated_at', 6)->nullable()->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_sellers');
    }
};
