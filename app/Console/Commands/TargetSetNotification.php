<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Target;
use App\Services\CommonFirebaseNotification;

class TargetSetNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:target-set-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set target reminder notification';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = today();
        $users = User::whereDoesntHave('targets', function ($q) use ($today) {
            $q->whereDate('date', $today);
        })
        ->whereNotNull('device_token')
        ->get();
        if ($users->isEmpty()) {
            return;
        }
        $tokens   = $users->pluck('device_token')->toArray();
        $userIds  = $users->pluck('id')->toArray();

        $title    = '🎯 Target Set Reminder';
        $body     = 'Please set your target before 7 AM';
        $dataPayload = [
            'type'               => 'daily_target',
            'android_channel_id' => 'normal_v5',
        ];
        $firebase = new CommonFirebaseNotification();
        $firebase->sendCommonNotification($tokens, $title, $body, ['payload' => json_encode($dataPayload)], $userIds);
    }
}
