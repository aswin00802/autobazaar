<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `user_informations` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('user_informations')) {
            return;
        }

        Schema::create('user_informations', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->string('profile', 100);
            $table->date('dob')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->text('address')->nullable();
            $table->integer('brand_id')->nullable();
            $table->integer('model_id')->nullable();
            $table->integer('fuel_id')->nullable();
            $table->string('vehicle_no', 255)->nullable();
            $table->string('millage', 255)->nullable();
            $table->unsignedTinyInteger('seating_capacity')->nullable();
            $table->string('driving_license_no', 255)->nullable();
            $table->string('driving_license_image', 255)->nullable();
            $table->date('driving_license_expiry')->nullable();
            $table->string('rc_book_no', 255)->nullable();
            $table->string('rc_book_image', 255)->nullable();
            $table->date('insurance_expiry')->nullable();
            $table->string('insurance_image', 255)->nullable();
            $table->string('permit_no', 255)->nullable();
            $table->date('permit_expiry')->nullable();
            $table->date('fitness_expiry')->nullable();
            $table->string('police_verification', 255)->nullable();
            $table->string('aadhaar_no', 20)->nullable();
            $table->string('aadhaar_copy', 255)->nullable();
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();

            $table->index(['user_id'], 'user_informations_user_id_index');
            $table->index(['fuel_id'], 'user_informations_fuel_id_index');
            $table->index(['gender'], 'user_informations_gender_index');
            $table->index(['blood_group'], 'user_informations_blood_group_index');
            $table->index(['brand_id'], 'user_informations_brand_id_index');
            $table->index(['model_id'], 'user_informations_model_id_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_informations');
    }
};
