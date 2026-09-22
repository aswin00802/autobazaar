<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CancelReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customerReasons = [
            'Changed my mind',
            'Booked by mistake',
            'Driver is too far',
            'Waiting time is too long',
            'Driver not moving',
            'Found another ride',
            'Fare is too high',
            'Driver asked to cancel',
            'Other',
        ];

        $driverReasons = [
            'Customer not reachable',
            'Customer not responding',
            'Pickup location incorrect',
            'Customer cancelled verbally',
            'Vehicle issue',
            'Traffic issue',
            'Personal emergency',
            'Too far pickup',
            'Other',
        ];

        // updateOrInsert, not insert: running the seeders twice used to leave
        // two of every reason in the list.
        foreach (['C' => $customerReasons, 'D' => $driverReasons] as $type => $reasons) {
            foreach ($reasons as $reason) {
                DB::table('cancel_reasons')->updateOrInsert(
                    ['type' => $type, 'name' => $reason],
                    ['updated_at' => now(), 'created_at' => now()],
                );
            }
        }
    }
}
