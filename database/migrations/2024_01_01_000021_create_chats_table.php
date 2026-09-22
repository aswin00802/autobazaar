<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `chats` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('chats')) {
            return;
        }

        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('message')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('video', 255)->nullable();
            $table->unsignedBigInteger('reply_to_id')->nullable();
            $table->enum('message_type', ['text', 'image', 'video', 'image_video', 'file', 'system'])->nullable()->default('text');
            $table->boolean('is_deleted')->nullable()->default(0);
            $table->date('message_date')->nullable();
            $table->time('message_time')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();

            $table->index(['reply_to_id'], 'fk_reply_to_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
