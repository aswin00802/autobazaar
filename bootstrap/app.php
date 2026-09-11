<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function ($schedule) {
        
        $schedule->command('chats:delete-old')
             ->hourly()
             ->withoutOverlapping()
             ->appendOutputTo(storage_path('logs/chats.log'));

        $schedule->command('fairprice:dispatch-prebook-rides')
             ->everyMinute()
             ->withoutOverlapping()
             ->appendOutputTo(storage_path('logs/fairprice-prebook.log'));
        
        // $randomMinute = rand(0, 59);
        // $schedule->command('test:cron')
        //          ->dailyAt(sprintf('06:%02d', $randomMinute))
        //          ->withoutOverlapping()
        //          ->appendOutputTo(storage_path('logs/cron.log'));
        
        // Random minute between 0–59
        // $randomMinute = rand(0, 59);
        // // Schedule command daily at 16:xx (4 PM)
        // $schedule->command('test:cron')
        //          ->dailyAt(sprintf('16:%02d', $randomMinute))
        //          ->withoutOverlapping()
        //          ->appendOutputTo(storage_path('logs/cron.log'))
        //          ->before(function () {
        //              \Log::info('Evening TestCron is about to run at ' . now());
        //          });
        
        $randomMinute = rand(0, 59);

        // Run daily between 6:00–6:59 AM
        $schedule->command('test:cron')
                 ->dailyAt(sprintf('06:%02d', $randomMinute))
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/cron.log'))
                 ->before(function () {
                    Log::info('Morning TestCron is about to run at ' . now());
                 });

        // //set target notification
        // $minutes = [5, 25, 50];

        // foreach ($minutes as $minute) {
        //     $schedule->command('app:target-set-notification')
        //         ->dailyAt("06:$minute")
        //         ->withoutOverlapping()
        //         ->appendOutputTo(storage_path('logs/target-reminder.log'));
        // }

        // //target notification close
        // $times = [
        //     '20:05', '20:25', '20:50',
        //     '21:05', '21:25', '21:50'
        // ];

        // foreach ($times as $time) {
        //     $schedule->command('app:target-close-reminder')
        //         ->dailyAt($time)
        //         ->withoutOverlapping()
        //         ->appendOutputTo(storage_path('logs/target-close.log'));
        // }

        // // Auto close
        // $schedule->command('app:target-auto-close')
        //     ->dailyAt('22:00')
        //     ->withoutOverlapping()
        //     ->appendOutputTo(storage_path('logs/target-auto-close.log'));
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.Userstatus' => \App\Http\Middleware\CheckUserStatus::class,
            'UserAuth' => \App\Http\Middleware\UserAuth::class,
            'sanctum' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function ($request, $throwable) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
