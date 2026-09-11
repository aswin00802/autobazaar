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
            $table->longtext('device_id')->nullable()->after('refresh_token');
            $table->longtext('referal_code')->nullable()->after('device_id');
            $table->string('last_login')->nullable()->after('referal_code');
            $table->integer('is_guest')->default(0)->after('last_login');
            $table->integer('seller')->default(0)->after('is_guest');
            $table->integer('vendor')->default(0)->after('seller');
            $table->integer('mechanic')->default(0)->after('vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fcm_token', 'device_id', 'referal_code','otp','otp_status','last_login','is_guest']);
        });
    }
};
