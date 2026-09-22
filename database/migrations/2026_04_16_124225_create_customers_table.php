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
        // Skips a table that is already there: the live database was built
        // from a dump, so many tables exist without this ever having run.
        Schema::hasTable('customers') || Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('f_name')->nullable();
            $table->string('l_name')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique();
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->date('dob')->nullable();
            $table->string('password')->nullable();
            $table->text('location')->nullable();
            $table->string('profile')->nullable();
            $table->string('device_token')->nullable();
            $table->string('device_id')->nullable();
            $table->integer('role_id')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->integer('status_id')->default(1)->comment('1=active, 2=deactive, 3=deleted, 4=permanent deleted');
            $table->string('current_location')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
