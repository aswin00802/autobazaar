<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * When each person was last doing something.
 *
 * `last_login` only tells you when someone signed in, and only the two apps
 * ever wrote it — the website never did. This is touched on any authenticated
 * request instead, so it covers all three, and "online" can be worked out from
 * it rather than from a flag an app might leave switched on after a crash.
 *
 * Nullable, so every existing row is simply unknown until that person next
 * does something. Nothing reads it except the admin Users list.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'last_seen_at')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('last_login');
            $table->index('last_seen_at', 'users_last_seen_at_index');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'last_seen_at')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_last_seen_at_index');
            $table->dropColumn('last_seen_at');
        });
    }
};
