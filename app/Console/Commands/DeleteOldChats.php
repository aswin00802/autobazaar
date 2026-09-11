<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Chat;
use Carbon\Carbon;

class DeleteOldChats extends Command
{
    protected $signature = 'chats:delete-old';
    protected $description = 'Delete chat messages older than 48 hours';

    public function handle()
    {
        $count = Chat::where('created_at', '<=', Carbon::now()->subHours(48))->delete();

        $this->info("Deleted $count old chat messages.");
    }
}
