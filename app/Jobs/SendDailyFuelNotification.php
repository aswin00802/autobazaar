<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Masters\AutoFuelType;
use App\Services\CommonFirebaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendDailyFuelNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
