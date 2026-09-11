<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('device_token');
            $table->string('provider_name')->nullable()->after('avatar');
            $table->string('provider_id')->nullable()->after('provider_name');
            $table->text('token')->nullable()->after('provider_id');
            $table->text('refresh_token')->nullable()->after('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'provider_name', 'provider_id','token','refresh_token']);
        });
    }
};
