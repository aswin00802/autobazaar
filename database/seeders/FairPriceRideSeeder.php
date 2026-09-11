<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\RideRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FairPriceRideSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('ride_request_drivers')->truncate();
        DB::table('ride_requests')->truncate();

        Schema::enableForeignKeyConstraints();

        $customers = $this->ensureCustomers();
        $drivers = $this->ensureDrivers();

        $now = Carbon::now();
        $pickupLat = 13.0850;
        $pickupLng = 80.2101;
        $dropLat = 13.0418;
        $dropLng = 80.2341;

        $rides = [
            [
                'customer_id' => $customers[0]->id,
                'driver_id' => $drivers[0]->id,
                'pickup_lat' => $pickupLat,
                'pickup_lng' => $pickupLng,
                'drop_lat' => $dropLat,
                'drop_lng' => $dropLng,
                'pickup' => 'Anna Nagar, Chennai',
                'drop' => 'T. Nagar, Chennai',
                'ride_for' => 'self',
                'passenger_count' => 1,
                'booking_type' => 'instant',
                'trip_distance_km' => 5.2,
                'pickup_distance_km' => 4.0,
                'trip_fare' => 93.60,
                'pickup_fare' => 36.00,
                'waiting_fare' => 10.00,
                'waiting_mins' => 10,
                'waiting_rate' => 2,
                'estimated_fare' => 93.60,
                'fare' => 139.60,
                'payment_status' => 'not_required',
                'collected_by' => 'hand',
                'status' => 'completed',
                'date' => $now->toDateString(),
                'accepted_at' => $now->copy()->subHours(3),
                'started_at' => $now->copy()->subHours(2)->subMinutes(50),
                'completed_at' => $now->copy()->subHours(2),
                'created_at' => $now->copy()->subHours(4),
                'updated_at' => $now->copy()->subHours(2),
            ],
            [
                'customer_id' => $customers[1]->id,
                'driver_id' => $drivers[1]->id,
                'pickup_lat' => $pickupLat,
                'pickup_lng' => $pickupLng,
                'drop_lat' => $dropLat,
                'drop_lng' => $dropLng,
                'pickup' => 'Velachery, Chennai',
                'drop' => 'Airport, Chennai',
                'ride_for' => 'other',
                'passenger_count' => 2,
                'other_phone' => '9876543210',
                'booking_type' => 'instant',
                'trip_distance_km' => 12.0,
                'trip_fare' => 216.00,
                'estimated_fare' => 216.00,
                'fare' => 216.00,
                'payment_status' => 'paid',
                'collected_by' => 'online',
                'razorpay_order_id' => 'order_dummy_instant_001',
                'razorpay_payment_id' => 'pay_dummy_instant_001',
                'paid_at' => $now->copy()->subDay(),
                'status' => 'completed',
                'date' => $now->copy()->subDay()->toDateString(),
                'accepted_at' => $now->copy()->subDay()->subHour(),
                'started_at' => $now->copy()->subDay()->subMinutes(45),
                'completed_at' => $now->copy()->subDay()->subMinutes(10),
                'created_at' => $now->copy()->subDay()->subHours(2),
                'updated_at' => $now->copy()->subDay()->subMinutes(10),
            ],
            [
                'customer_id' => $customers[0]->id,
                'driver_id' => null,
                'pickup_lat' => $pickupLat,
                'pickup_lng' => $pickupLng,
                'drop_lat' => $dropLat,
                'drop_lng' => $dropLng,
                'pickup' => 'Anna Nagar, Chennai',
                'drop' => 'OMR, Chennai',
                'ride_for' => 'self',
                'passenger_count' => 1,
                'booking_type' => 'instant',
                'trip_distance_km' => 8.0,
                'trip_fare' => 144.00,
                'estimated_fare' => 144.00,
                'payment_status' => 'not_required',
                'status' => 'pending',
                'date' => $now->toDateString(),
                'created_at' => $now->copy()->subMinutes(15),
                'updated_at' => $now->copy()->subMinutes(15),
            ],
            [
                'customer_id' => $customers[2]->id,
                'driver_id' => $drivers[0]->id,
                'pickup_lat' => $pickupLat,
                'pickup_lng' => $pickupLng,
                'drop_lat' => $dropLat,
                'drop_lng' => $dropLng,
                'pickup' => 'Adyar, Chennai',
                'drop' => 'Egmore, Chennai',
                'ride_for' => 'self',
                'passenger_count' => 3,
                'booking_type' => 'instant',
                'trip_distance_km' => 6.5,
                'trip_fare' => 117.00,
                'pickup_fare' => 18.00,
                'estimated_fare' => 117.00,
                'payment_status' => 'not_required',
                'status' => 'accepted',
                'date' => $now->toDateString(),
                'accepted_at' => $now->copy()->subMinutes(20),
                'created_at' => $now->copy()->subMinutes(30),
                'updated_at' => $now->copy()->subMinutes(20),
            ],
            [
                'customer_id' => $customers[1]->id,
                'driver_id' => $drivers[1]->id,
                'pickup_lat' => $pickupLat,
                'pickup_lng' => $pickupLng,
                'drop_lat' => $dropLat,
                'drop_lng' => $dropLng,
                'pickup' => 'Nungambakkam, Chennai',
                'drop' => 'Guindy, Chennai',
                'ride_for' => 'other',
                'passenger_count' => 1,
                'other_phone' => '9123456789',
                'booking_type' => 'prebook',
                'scheduled_at' => $now->copy()->addDay()->setTime(14, 30),
                'trip_distance_km' => 7.0,
                'trip_fare' => 126.00,
                'estimated_fare' => 126.00,
                'advance_amount' => 126.00,
                'payment_status' => 'paid',
                'razorpay_order_id' => 'order_dummy_prebook_001',
                'razorpay_payment_id' => 'pay_dummy_prebook_001',
                'paid_at' => $now->copy()->subHours(5),
                'status' => 'scheduled',
                'date' => $now->toDateString(),
                'created_at' => $now->copy()->subHours(6),
                'updated_at' => $now->copy()->subHours(5),
            ],
        ];

        foreach ($rides as $ride) {
            RideRequest::create($ride);
        }

        $this->command?->info('FairPrice dummy rides seeded: ' . count($rides) . ' records.');
    }

    private function ensureCustomers(): array
    {
        $defaults = [
            ['name' => 'Demo Customer One', 'phone' => '9000000001'],
            ['name' => 'Demo Customer Two', 'phone' => '9000000002'],
            ['name' => 'Demo Customer Three', 'phone' => '9000000003'],
        ];

        $customers = [];

        foreach ($defaults as $data) {
            $customers[] = Customer::firstOrCreate(
                ['phone' => $data['phone']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('123456'),
                    'status_id' => 1,
                ]
            );
        }

        return $customers;
    }

    private function ensureDrivers(): array
    {
        $defaults = [
            ['name' => 'Demo Driver One', 'phone_number' => '9100000001'],
            ['name' => 'Demo Driver Two', 'phone_number' => '9100000002'],
        ];

        $drivers = [];

        foreach ($defaults as $index => $data) {
            $drivers[] = User::updateOrCreate(
                ['phone_number' => $data['phone_number']],
                [
                    'name' => $data['name'],
                    'email' => 'demo.driver' . ($index + 1) . '@fairprice.test',
                    'password' => bcrypt('123456'),
                    'status' => 1,
                    'latitude' => 13.0827,
                    'longitude' => 80.2707,
                ]
            );
        }

        return $drivers;
    }
}
