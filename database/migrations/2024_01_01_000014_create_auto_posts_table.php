<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_posts` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_posts')) {
            return;
        }

        Schema::create('auto_posts', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('auto_unique_id', 25)->nullable();
            $table->integer('user_id');
            $table->bigInteger('auto_brand_id')->nullable();
            $table->integer('auto_model_id')->default(0);
            $table->string('specific_model', 50)->nullable();
            $table->bigInteger('auto_body_type_id')->nullable();
            $table->string('image_1', 50)->nullable();
            $table->string('image_2', 50)->nullable();
            $table->string('image_3', 50)->nullable();
            $table->string('image_4', 50)->nullable();
            $table->string('image_5', 50)->nullable();
            $table->string('image_6', 50)->nullable();
            $table->string('registration_year', 45)->nullable();
            $table->string('registration_number', 45)->nullable();
            $table->string('owner', 45)->nullable();
            $table->bigInteger('fuel_type_id')->nullable();
            $table->bigInteger('transmission_type_id')->nullable();
            $table->string('kilometer', 45)->nullable();
            $table->string('price', 100)->nullable();
            $table->string('price_expectations', 45)->nullable();
            $table->string('descriptions', 45)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('address', 45)->nullable();
            $table->string('name', 45)->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->string('auto_usage_status', 100);
            $table->string('rto', 100)->nullable();
            $table->string('condition', 100)->nullable();
            $table->string('rc_status', 100)->nullable();
            $table->string('loan_status', 100)->nullable();
            $table->string('noc_status', 100)->nullable();
            $table->string('fc_status', 100)->nullable();
            $table->string('permit_status', 100)->nullable();
            $table->string('insurance', 100)->nullable();
            $table->string('financial_availability', 50)->nullable()->default('false');
            $table->string('loan_amount', 50)->nullable();
            $table->string('first_payment', 50)->nullable();
            $table->string('emi_amount', 50)->nullable();
            $table->string('no_of_months', 50)->nullable();
            $table->string('chellan_status', 50)->nullable();
            $table->string('accident_status', 50)->nullable();
            $table->string('orp', 100)->nullable();
            $table->string('crass_weight', 100)->nullable();
            $table->string('passenger_capacity', 100)->nullable();
            $table->string('ground_clearance', 100)->nullable();
            $table->string('gear', 100)->nullable();
            $table->string('millage', 7)->nullable();
            $table->string('vehicle_suitable', 100)->nullable();
            $table->string('engine_cc', 20)->nullable();
            $table->string('free_service', 20)->nullable();
            $table->string('finance_arrangements', 100)->nullable();
            $table->string('auto_status', 10)->nullable();
            $table->integer('status')->nullable();
            $table->string('post_type', 20)->nullable();
            $table->text('remark')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index(['auto_usage_status', 'auto_status'], 'perf_auto_posts_auto_usage_status_auto_status_idx');
            $table->index(['auto_status'], 'perf_auto_posts_auto_status_idx');
            $table->index(['user_id'], 'perf_auto_posts_user_id_idx');
            $table->index(['created_at'], 'perf_auto_posts_created_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_posts');
    }
};
