<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Messages to a customer that live on the site — "your order has been packed",
 * and so on. Laravel's own shape, so the framework handles reading, marking as
 * read and counting.
 *
 * The push sent to the mobile app goes out at the same moment but is not stored
 * here; this is what someone sees under My Account, and what they can still
 * find days later when the push has long gone from their phone.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('notifications')) {
            return;
        }

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            // "unread ones for this person, newest first" is the only query it gets
            $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'notifications_unread_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
