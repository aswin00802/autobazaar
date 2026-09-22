<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_enquiries` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_enquiries')) {
            return;
        }

        Schema::create('auto_enquiries', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->bigInteger('post_id');
            $table->integer('interested_status');
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();

            $table->index(['user_id'], 'enquiries_user');
            $table->index(['post_id'], 'enquiries_post');
            $table->index(['created_at'], 'perf_auto_enquiries_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_enquiries');
    }
};
