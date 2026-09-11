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

        foreach ($customerReasons as $reason) {
            DB::table('cancel_reasons')->insert([
                'type'       => 'C',
                'name'       => $reason,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach ($driverReasons as $reason) {
            DB::table('cancel_reasons')->insert([
                'type'       => 'D',
                'name'       => $reason,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
