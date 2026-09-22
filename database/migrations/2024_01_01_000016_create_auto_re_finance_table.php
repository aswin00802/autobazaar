<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `auto_re_finance` — read from the live database, which had no migration for it.
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
        if (Schema::hasTable('auto_re_finance')) {
            return;
        }

        Schema::create('auto_re_finance', function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('user_id');
            $table->integer('brand_id');
            $table->string('specific_model', 30);
            $table->string('registration_year', 10);
            $table->string('registration_number', 25);
            $table->integer('status')->default(1);
            $table->timestamp('created_at', 6)->useCurrent();
            $table->timestamp('updated_at', 6)->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_re_finance');
    }
};
