<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

class HotelSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/data/world_hotels_dataset.csv');

        if (!file_exists($csvFile)) {
            $this->command->error('CSV file not found!');
            return;
        }

        $file = fopen($csvFile, 'r');

        // Skip the header row
        fgetcsv($file);

        while (($row = fgetcsv($file, 0, ',')) !== false) {

            Hotel::create([
                'name' => $row[1],
                'city' => $row[2],
                'neighborhood' => $row[3],
                'distance_km' => $row[4],
                'price_per_night' => $row[5],
                'rating' => $row[6],
                'review_count' => $row[7],
                'amenities' => $row[8],
                'available_rooms' => $row[9],
            ]);
        }

        fclose($file);
    }
}