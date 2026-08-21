<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Factory;

class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        $cities = [
            [
                'name' => 'Cairo',
                'code2' => 'EG',
                'image' => 'https://images.unsplash.com/photo-1572252009286-268acec5ca0a'
            ],
            [
                'name' => 'Alexandria',
                'code2' => 'EG',
                'image' => 'https://images.unsplash.com/photo-1591604466107-ec97de577aff'
            ],
            [
                'name' => 'Paris',
                'code2' => 'FR',
                'image' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34'
            ],
            [
                'name' => 'London',
                'code2' => 'GB',
                'image' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad'
            ],
            [
                'name' => 'Rome',
                'code2' => 'IT',
                'image' => 'https://images.unsplash.com/photo-1529260830199-42c24126f198'
            ],
            [
                'name' => 'Madrid',
                'code2' => 'ES',
                'image' => 'https://images.unsplash.com/photo-1539037116277-4db20889f2d4'
            ],
            [
                'name' => 'Berlin',
                'code2' => 'DE',
                'image' => 'https://images.unsplash.com/photo-1560969184-10fe8719e047'
            ],
            [
                'name' => 'Amsterdam',
                'code2' => 'NL',
                'image' => 'https://images.unsplash.com/photo-1534351590666-13e3e96b5017'
            ],
            [
                'name' => 'Dubai',
                'code2' => 'AE',
                'image' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c'
            ],
            [
                'name' => 'Abu Dhabi',
                'code2' => 'AE',
                'image' => 'https://images.unsplash.com/photo-1546412414-e1885259563a'
            ],
            [
                'name' => 'Doha',
                'code2' => 'QA',
                'image' => 'https://images.unsplash.com/photo-1582972236019-ea4af5ffe587'
            ],
            [
                'name' => 'Istanbul',
                'code2' => 'TR',
                'image' => 'https://images.unsplash.com/photo-1524231757912-21f4fe3a7200'
            ],
            [
                'name' => 'Tokyo',
                'code2' => 'JP',
                'image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf'
            ],
            [
                'name' => 'Osaka',
                'code2' => 'JP',
                'image' => 'https://images.unsplash.com/photo-1590559899731-a382839e5549'
            ],
            [
                'name' => 'Seoul',
                'code2' => 'KR',
                'image' => 'https://images.unsplash.com/photo-1538485399081-7c897c9d1b4b'
            ],
            [
                'name' => 'Beijing',
                'code2' => 'CN',
                'image' => 'https://images.unsplash.com/photo-1508804185872-d7badad00f7d'
            ],
            [
                'name' => 'Shanghai',
                'code2' => 'CN',
                'image' => 'https://images.unsplash.com/photo-1548919973-5cef591cdbc9'
            ],
            [
                'name' => 'Bangkok',
                'code2' => 'TH',
                'image' => 'https://images.unsplash.com/photo-1508009603885-50cf7c579365'
            ],
            [
                'name' => 'Singapore',
                'code2' => 'SG',
                'image' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd'
            ],
            [
                'name' => 'New York',
                'code2' => 'US',
                'image' => 'https://images.unsplash.com/photo-1496588152823-86ff7695e68f'
            ],
            [
                'name' => 'Los Angeles',
                'code2' => 'US',
                'image' => 'https://images.unsplash.com/photo-1515896769750-31548aa180ed'
            ],
            [
                'name' => 'Chicago',
                'code2' => 'US',
                'image' => 'https://images.unsplash.com/photo-1494522855154-9297ac14b55f'
            ],
            [
                'name' => 'San Francisco',
                'code2' => 'US',
                'image' => 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29'
            ],
            [
                'name' => 'Toronto',
                'code2' => 'CA',
                'image' => 'https://images.unsplash.com/photo-1517090504586-fde19ea6066f'
            ],
            [
                'name' => 'Vancouver',
                'code2' => 'CA',
                'image' => 'https://images.unsplash.com/photo-1559511260-66a654ae982a'
            ],
            [
                'name' => 'Sydney',
                'code2' => 'AU',
                'image' => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9'
            ],
            [
                'name' => 'Melbourne',
                'code2' => 'AU',
                'image' => 'https://images.unsplash.com/photo-1514395462725-fb4566210144'
            ],
            [
                'name' => 'Rio de Janeiro',
                'code2' => 'BR',
                'image' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325'
            ],
            [
                'name' => 'São Paulo',
                'code2' => 'BR',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab'
            ],
            [
                'name' => 'Cape Town',
                'code2' => 'ZA',
                'image' => 'https://images.unsplash.com/photo-1580060839134-75a5edca2e99'
            ],
        ];

       $city = fake()->randomElement($cities);

$country = Country::where('code2', $city['code2'])->firstOrFail();

return [
    'country_id' => $country->id,
    'name' => $city['name'],
    'description' => fake()->sentence(),
    'image' => $city['image'],
];
    }
}