<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    // protected function schedule(Schedule $schedule): void
    // {
    //     // $schedule->command('inspire')->hourly();
    // }
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('chats:delete-old')->hourly();

        // 6–7 AM random dispatch
        // $randomMinute = rand(0, 59);
        // $hour = 6;

        // $schedule->job(new \App\Jobs\SendDailyFuelNotification())
        //      ->dailyAt(sprintf('%02d:%02d', $hour, $randomMinute));

        // $schedule->job(new \App\Jobs\SendDailyFuelNotification())
        //      ->everyMinute();

        // $schedule->call(function() {
        //         \Log::info("Scheduler is working!");
        //     })->everyMinute();

        $schedule->command('test:cron')
             ->everyMinute() // local testing-க்கு immediate run
            //  ->dailyAt('06:00')    
             ->withoutOverlapping()
             ->sendOutputTo(storage_path('logs/cron.log')); // output capture
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
