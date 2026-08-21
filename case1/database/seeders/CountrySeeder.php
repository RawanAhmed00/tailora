<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $limit = 100;
        $offset = 0;
        $more = true;

        while ($more) {

            $response = Http::withToken(env('COUNTRIES_TOKEN'))
                ->acceptJson()
                ->get(env('COUNTRIES_BASE_URL'), [
                    'limit' => $limit,
                    'offset' => $offset,
                    'response_fields' => 'names.common,names.official,codes.alpha_2,codes.alpha_3,flag.url_png',
                ]);

            if (! $response->successful()) {
                $this->command->error('Failed to fetch countries.');
                return;
            }

            $countries = data_get($response->json(), 'data.objects', []);

            foreach ($countries as $country) {

                $code2 = data_get($country, 'codes.alpha_2');

                if (blank($code2)) {
                    continue;
                }

                Country::updateOrCreate(
                    [
                        'code2' => strtoupper($code2),
                    ],
                    [
                        'name' => data_get($country, 'names.common'),
                        'official_name' => data_get($country, 'names.official'),
                        'code3' => data_get($country, 'codes.alpha_3'),
                        'flag' => data_get($country, 'flag.url_png'),
                    ]
                );
            }

            $more = data_get($response->json(), 'data.meta.more', false);

            $offset += $limit;
        }

        $this->command->info('Countries seeded successfully.');
    }
}