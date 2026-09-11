<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE ride_messages MODIFY message_type ENUM('text', 'voice_note', 'image', 'video') NOT NULL"
        );
    }

    public function down(): void
    {
        DB::statement(
            "ALTER TABLE ride_messages MODIFY message_type ENUM('text', 'voice_note') NOT NULL"
        );
    }
};
