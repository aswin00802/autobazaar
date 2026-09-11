<?php

namespace App\Console\Commands;

use App\Models\Masters\AutoFuelType;
use App\Models\User;
use App\Services\CommonFirebaseNotification;
use Illuminate\Console\Command;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Fetch fuels with price
        $fuels = AutoFuelType::whereIn('name', ['CNG','LPG'])
            ->whereNotNull('price')
            ->where('status', 1)
            ->get(['id','name','price']);

        if ($fuels->isEmpty()) return; // price illa na send pannadhu

        // Users role_id=1000
        // $users = User::where('role_id', 1000)
        //     ->whereNotNull('device_token')
        //     ->get();
        $users = User::where('role_id', 1000)
            ->whereNotNull('device_token')
            ->where('id','1')
            ->get();

        if ($users->isEmpty()) return;

        // Prepare message: only fuels with price
        $fuelsWithPrice = $fuels->filter(fn($fuel) => !is_null($fuel->price));

        if ($fuelsWithPrice->isEmpty()) return; // price illa na skip

        $title = "Today's Fuel Prices";
        $body  = $fuelsWithPrice->map(fn($fuel) => $fuel->name.' Price: '.$fuel->price)
                                ->implode(', ');

        $tokens  = $users->pluck('device_token')->toArray();
        $userIds = $users->pluck('id')->toArray();

        // Send via Firebase
        $firebase = new CommonFirebaseNotification();
        $firebase->sendCommonNotification($tokens, $title, $body, [], $userIds);
    }
}
