<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ride_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('ride_id');
            $table->enum('sender_type', ['customer', 'driver', 'system']);
            $table->unsignedBigInteger('sender_id');
            $table->enum('message_type', ['text', 'voice_note']);
            $table->text('text')->nullable();
            $table->string('media_path')->nullable();
            $table->string('media_mime', 100)->nullable();
            $table->unsignedInteger('media_size_bytes')->nullable();
            $table->unsignedSmallInteger('duration_sec')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamp('customer_read_at')->nullable();
            $table->timestamp('driver_read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['ride_id', 'created_at']);
            $table->index(['sender_type', 'sender_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ride_messages');
    }
};

