<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `feedbacks` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('feedbacks')) {
            return;
        }

        Schema::create('feedbacks', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('user_id');
            $table->text('message');
            $table->integer('rating')->default(0);
            $table->integer('status_id')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->string('ip_address', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
