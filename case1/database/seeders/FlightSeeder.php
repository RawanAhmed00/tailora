<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('flights')->insert([
            [
                'user_id' => 3, 
                'ignav_id' => 'LG12345',
                'origin' => 'CAI',
                'destination' => 'DXB',
                'departure_date' => '2026-09-01',
                'return_date' => '2026-09-10',
                'outbound_carrier_code' => 'MS',
                'outbound_flight_number' => 'MS910',
                'inbound_carrier_code' => 'MS',
                'inbound_flight_number' => 'MS911',
                'airline' => 'EgyptAir',
                'price' => 450.00,
                'currency' => 'USD',
                'booking_url' => 'https://example.com/booking/3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 4,
                'ignav_id' => 'LG67890',
                'origin' => 'JED',
                'destination' => 'CAI',
                'departure_date' => '2026-10-05',
                'return_date' => null,
                'outbound_carrier_code' => 'SV',
                'outbound_flight_number' => 'SV301',
                'inbound_carrier_code' => null,
                'inbound_flight_number' => null,
                'airline' => 'Saudia',
                'price' => 300.50,
                'currency' => 'USD',
                'booking_url' => 'https://example.com/booking/4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}