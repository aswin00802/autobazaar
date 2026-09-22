<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The 10 foreign keys belonging to the tables that were read out of the
 * live database (the 2024_01_01_* migrations), plus the one on `trips` whose
 * own migration is dated before the table it points at.
 *
 * They live here, after every table exists, so no table has to be created in
 * any particular order.
 *
 * Every key is added only if it is not already there, so this is safe to run
 * against a database that already has them.
 */
return new class extends Migration
{
    /** Is this constraint already on the table? */
    private function has(string $table, string $name): bool
    {
        return DB::table('information_schema.TABLE_CONSTRAINTS')
            ->where('CONSTRAINT_SCHEMA', DB::getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('CONSTRAINT_NAME', $name)
            ->exists();
    }

    public function up(): void
    {
        Schema::table('auto_enquiries', function (Blueprint $table) {
            if (! $this->has('auto_enquiries', 'enquiries_post')) {
                $table->foreign('post_id', 'enquiries_post')->references('id')->on('auto_posts')->onDelete('cascade')->onUpdate('cascade');
            }
            if (! $this->has('auto_enquiries', 'enquiries_user')) {
                $table->foreign('user_id', 'enquiries_user')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            }
        });

        Schema::table('auto_favorites', function (Blueprint $table) {
            if (! $this->has('auto_favorites', 'favourites_post')) {
                $table->foreign('post_id', 'favourites_post')->references('id')->on('auto_posts')->onDelete('cascade')->onUpdate('cascade');
            }
            if (! $this->has('auto_favorites', 'favourites_user')) {
                $table->foreign('user_id', 'favourites_user')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            }
        });

        Schema::table('auto_insurance', function (Blueprint $table) {
            if (! $this->has('auto_insurance', 'brand_id')) {
                $table->foreign('brand_id', 'brand_id')->references('id')->on('auto_brands')->onDelete('cascade')->onUpdate('cascade');
            }
            if (! $this->has('auto_insurance', 'user_id')) {
                $table->foreign('user_id', 'user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            }
        });

        Schema::table('auto_rto_services', function (Blueprint $table) {
            if (! $this->has('auto_rto_services', 'user')) {
                $table->foreign('user_id', 'user')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            }
        });

        Schema::table('chats', function (Blueprint $table) {
            if (! $this->has('chats', 'fk_reply_to_id')) {
                $table->foreign('reply_to_id', 'fk_reply_to_id')->references('id')->on('chats')->onDelete('set null');
            }
        });

        Schema::table('preferred_rides', function (Blueprint $table) {
            if (! $this->has('preferred_rides', 'preferred_rides_user_id_foreign')) {
                $table->foreign('user_id', 'preferred_rides_user_id_foreign')->references('id')->on('users');
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (! $this->has('trips', 'trips_target_id_foreign')) {
                $table->foreign('target_id', 'trips_target_id_foreign')->references('id')->on('targets')->onDelete('cascade');
            }
        });
    }

    public function down(): void
    {
        Schema::table('auto_enquiries', function (Blueprint $table) {
            if ($this->has('auto_enquiries', 'enquiries_post')) {
                $table->dropForeign('enquiries_post');
            }
            if ($this->has('auto_enquiries', 'enquiries_user')) {
                $table->dropForeign('enquiries_user');
            }
        });

        Schema::table('auto_favorites', function (Blueprint $table) {
            if ($this->has('auto_favorites', 'favourites_post')) {
                $table->dropForeign('favourites_post');
            }
            if ($this->has('auto_favorites', 'favourites_user')) {
                $table->dropForeign('favourites_user');
            }
        });

        Schema::table('auto_insurance', function (Blueprint $table) {
            if ($this->has('auto_insurance', 'brand_id')) {
                $table->dropForeign('brand_id');
            }
            if ($this->has('auto_insurance', 'user_id')) {
                $table->dropForeign('user_id');
            }
        });

        Schema::table('auto_rto_services', function (Blueprint $table) {
            if ($this->has('auto_rto_services', 'user')) {
                $table->dropForeign('user');
            }
        });

        Schema::table('chats', function (Blueprint $table) {
            if ($this->has('chats', 'fk_reply_to_id')) {
                $table->dropForeign('fk_reply_to_id');
            }
        });

        Schema::table('preferred_rides', function (Blueprint $table) {
            if ($this->has('preferred_rides', 'preferred_rides_user_id_foreign')) {
                $table->dropForeign('preferred_rides_user_id_foreign');
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if ($this->has('trips', 'trips_target_id_foreign')) {
                $table->dropForeign('trips_target_id_foreign');
            }
        });
    }
};
