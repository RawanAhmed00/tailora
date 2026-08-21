<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $hotels = Hotel::all();

        if ($users->isEmpty() || $hotels->isEmpty()) {
            return;
        }

        foreach ($users as $user) {

            $hotel = $hotels->random();

        $numberOfNights = rand(1, 7);

        $totalPrice = $hotel->price_per_night * $numberOfNights;

            Booking::create([
    'user_id' => $user->id,
    'hotel_id' => $hotel->id,
    'number_of_nights' => $numberOfNights,
    'total_price' => $totalPrice,
    'status' => collect([
        'pending',
        'confirmed',
        'cancelled'])->random(),
    ]);
        }
    }
}