<?php

namespace Database\Factories;

use App\Models\Attraction;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttractionFactory extends Factory
{
    protected $model = Attraction::class;

    public function definition(): array
    {
        $attractions = [
            [
                'name' => 'Pyramids of Giza',
                'city' => 'Cairo',
                'price' => 25,
                'image' => 'https://images.unsplash.com/photo-1568322445389-f64ac2515020'
            ],
            [
                'name' => 'Egyptian Museum',
                'city' => 'Cairo',
                'price' => 15,
                'image' => 'https://images.unsplash.com/photo-1564399579883-451a5d44ec08'
            ],
            [
                'name' => 'Eiffel Tower',
                'city' => 'Paris',
                'price' => 35,
                'image' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34'
            ],
            [
                'name' => 'Colosseum',
                'city' => 'Rome',
                'price' => 28,
                'image' => 'https://images.unsplash.com/photo-1552832230-c0197dd311b5'
            ],
            [
                'name' => 'Burj Khalifa',
                'city' => 'Dubai',
                'price' => 55,
                'image' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c'
            ],
            [
                'name' => 'Sheikh Zayed Grand Mosque',
                'city' => 'Abu Dhabi',
                'price' => 0,
                'image' => 'https://images.unsplash.com/photo-1564769625905-50e93615e769'
            ],
            [
                'name' => 'Hagia Sophia',
                'city' => 'Istanbul',
                'price' => 25,
                'image' => 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200'
            ],
            [
                'name' => 'Tokyo Tower',
                'city' => 'Tokyo',
                'price' => 22,
                'image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf'
            ],
            [
                'name' => 'Statue of Liberty',
                'city' => 'New York',
                'price' => 28,
                'image' => 'https://images.unsplash.com/photo-1539650116574-75c0c6d73f6e'
            ],
            [
                'name' => 'Sydney Opera House',
                'city' => 'Sydney',
                'price' => 40,
                'image' => 'https://images.unsplash.com/photo-1523059623039-a9ed027e7fad'
            ],
        ];

        $place = fake()->randomElement($attractions);

        $city = City::firstOrCreate([
            'name' => $place['city'],
        ]);

        return [
            'city_id' => $city->id,
            'name' => $place['name'],
            'description' => fake()->paragraph(),
            'latitude' => fake()->latitude(),
            'longitude' => fake()->longitude(),
            'image' => $place['image'],
            'price' => $place['price'],
        ];
    }
}