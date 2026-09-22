<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_favorites` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_favorites')) {
            return;
        }

        Schema::create('auto_favorites', function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('user_id');
            $table->bigInteger('post_id');
            $table->integer('fav_status')->nullable();
            $table->integer('status')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index(['post_id'], 'favourites_post');
            $table->index(['user_id'], 'favourites_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_favorites');
    }
};
