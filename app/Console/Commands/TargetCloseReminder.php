<?php

namespace App\Console\Commands;

use App\Models\Target;
use App\Services\CommonFirebaseNotification;
use Illuminate\Console\Command;

class TargetCloseReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:target-close-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send target close reminder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = today();
        $targets = Target::whereDate('date', $today)
                        ->where('is_closed', false)
                        ->with('user')
                        ->get();

        if ($targets->isEmpty()) {
            return;
        }
        $tokens = [];
        $userIds = [];

        foreach ($targets as $target) {
            if ($target->user->device_token) {
                $tokens[]  = $target->user->device_token;
                $userIds[] = $target->user->id;
            }
        }

        $title    = '🎯 Target Close Reminder';
        $body     = 'Please close your today target before 10 PM';
        $dataPayload = [
            'type'               => 'target_close',
            'android_channel_id' => 'normal_v5',
        ];
        $firebase = new CommonFirebaseNotification();
        $firebase->sendCommonNotification($tokens, $title, $body, ['payload' => json_encode($dataPayload)], $userIds);
    }
}
