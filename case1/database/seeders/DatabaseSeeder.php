<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            TourGuideSeeder::class,
            CategorySeeder::class,
            CountrySeeder::class,
            HotelSeeder::class,
            RestaurantSeeder::class,
            FlightSeeder::class,

        
        ]);

        User::factory(20)->create();

        City::factory(250)->create();

        Attraction::factory(1000)->create();

        $this->call([
            AttractionCategorySeeder::class,
            BookingSeeder::class,
        ]);

       
   
    }
}