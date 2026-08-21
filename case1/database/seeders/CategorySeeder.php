<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Beaches'],
            ['name' => 'Mountains'],
            ['name' => 'Museums'],
            ['name' => 'Historical Sites'],
            ['name' => 'Adventure'],
            ['name' => 'Shopping'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']]
            );
        }
    }
}