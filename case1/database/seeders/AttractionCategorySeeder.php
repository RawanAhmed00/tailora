<?php

namespace Database\Seeders;

use App\Models\Attraction;
use App\Models\Category;
use Illuminate\Database\Seeder;

class AttractionCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categoryIds = Category::pluck('id');

        Attraction::all()->each(function ($attraction) use ($categoryIds) {
            $attraction->categories()->sync(
                $categoryIds->random(rand(1, 3))->toArray()
            );
        });
    }
}