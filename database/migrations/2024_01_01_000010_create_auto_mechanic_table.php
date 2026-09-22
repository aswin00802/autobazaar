<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_mechanic` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_mechanic')) {
            return;
        }

        Schema::create('auto_mechanic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('shop_name', 200)->nullable();
            $table->string('location', 200)->nullable();
            $table->string('address', 200);
            $table->string('contact', 25)->nullable();
            $table->string('alternate', 15)->nullable();
            $table->string('category', 200)->nullable();
            $table->integer('status_id')->default(1);
            $table->timestamp('created_at', 6)->useCurrent()->useCurrentOnUpdate();
            $table->timestamp('updated_at', 6)->default('0000-00-00 00:00:00.000000');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_mechanic');
    }
};
