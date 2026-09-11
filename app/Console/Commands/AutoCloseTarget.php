<?php

namespace App\Console\Commands;

use App\Models\Target;
use Illuminate\Console\Command;

class AutoCloseTarget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-close-target';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto close targets';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Target::whereDate('date', today())
            ->where('status_id', 1)
            ->update([
                'status_id' => 2,
            ]);
    }
}
