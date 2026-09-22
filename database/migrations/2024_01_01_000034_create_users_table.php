<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `users` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name', 255)->nullable();
            $table->string('f_name', 255)->nullable();
            $table->string('l_name', 255)->nullable();
            $table->string('email', 200)->nullable();
            $table->string('phone_number', 250);
            $table->string('password', 100)->nullable();
            $table->bigInteger('auto_area_id')->nullable();
            $table->integer('role_id')->default(1000);
            $table->integer('otp')->nullable();
            $table->string('device_token', 255)->nullable();
            $table->string('selected_mode', 50)->nullable()->comment('Stores the currently selected application mode');
            $table->string('avatar', 255)->nullable();
            $table->string('provider_name', 255)->nullable();
            $table->string('provider_id', 255)->nullable();
            $table->text('token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->longText('device_id')->nullable();
            $table->longText('referal_code')->nullable();
            $table->string('last_login', 255)->nullable();
            $table->integer('is_guest')->default(0);
            $table->integer('seller')->default(0);
            $table->integer('vendor')->default(0);
            $table->integer('mechanic')->default(0);
            $table->integer('status')->nullable()->default(1);
            $table->boolean('fair_price_enabled')->default(0);
            $table->boolean('is_online')->default(0);
            $table->boolean('is_available')->default(0);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('current_location', 255);

            $table->index(['latitude', 'longitude'], 'users_latitude_longitude_index');
            $table->index(['is_online'], 'users_is_online_index');
            $table->index(['is_available'], 'users_is_available_index');
            $table->index(['phone_number'], 'perf_users_phone_number_idx');
            $table->index(['created_at'], 'perf_users_created_at_idx');
            $table->index(['status'], 'perf_users_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
