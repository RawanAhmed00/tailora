<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    public function run(): void
    {
        $file = database_path('seeders/data/restaurants_dataset.csv');

        if (!file_exists($file)) {
            $this->command->error('CSV file not found.');
            return;
        }

        $csv = fopen($file, 'r');

        $header = fgetcsv($csv);

        while (($row = fgetcsv($csv)) !== false) {

            $data = array_combine($header, $row);

            Restaurant::create([
                'name' => $data['Restaurant Name'],
                'city' => $data['City'],
                'address' => $data['Address'],
                'locality' => $data['Locality'],
                'latitude' => $data['Latitude'],
                'longitude' => $data['Longitude'],
                'cuisines' => $data['Cuisines'],
                'average_cost_for_two' => $data['Average Cost for two'],
                'currency' => $data['Currency'],
                'has_table_booking' => strtolower($data['Has Table booking']) === 'yes',
                'has_online_delivery' => strtolower($data['Has Online delivery']) === 'yes',
                'is_delivering_now' => strtolower($data['Is delivering now']) === 'yes',
                'price_range' => $data['Price range'],
                'rating' => $data['Aggregate rating'],
                'votes' => $data['Votes'],
            ]);
        }

        fclose($csv);
    }
}