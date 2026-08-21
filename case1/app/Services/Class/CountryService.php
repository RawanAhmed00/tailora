<?php

namespace App\Services\Class;

use App\Models\Country;
use App\Services\Interfaces\ICountryService;

class CountryService implements ICountryService
{
    private function formatCountry(Country $country): array
    {
        return [
            'id' => $country->id,
            'name' => $country->name,
            'official_name' => $country->official_name,
            'code2' => $country->code2,
            'code3' => $country->code3,
            'flag' => [
                'png' => $country->flag,
                'svg' => $country->flag,
                'emoji' => '',
            ],
            // Nested structure for backward compatibility with frontend scripts expecting country_information
            'country_information' => [
                'id' => $country->id,
                'name' => $country->name,
                'official_name' => $country->official_name,
                'code2' => $country->code2,
                'code3' => $country->code3,
            ],
            'capital' => null,
            'currency' => [],
            'languages' => [],
        ];
    }

    public function getCountries(array $filters = [])
    {
        $query = Country::query();

        if (!empty($filters['q']) || !empty($filters['query']) || !empty($filters['search'])) {
            $q = $filters['q'] ?? $filters['query'] ?? $filters['search'];
            $query->where(function ($w) use ($q) {
                $w->where('name', 'LIKE', "%{$q}%")
                  ->orWhere('official_name', 'LIKE', "%{$q}%")
                  ->orWhere('code2', 'LIKE', "%{$q}%")
                  ->orWhere('code3', 'LIKE', "%{$q}%");
            });
        }

        if (!empty($filters['name'])) {
            $name = $filters['name'];
            $query->where(function ($w) use ($name) {
                $w->where('name', 'LIKE', "%{$name}%")
                  ->orWhere('official_name', 'LIKE', "%{$name}%");
            });
        }

        if (!empty($filters['code2'])) {
            $query->where('code2', strtoupper($filters['code2']));
        }

        if (!empty($filters['code3'])) {
            $query->where('code3', strtoupper($filters['code3']));
        }

        if (!empty($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        $countries = $query->orderBy('name')->get();

        return $countries->map(fn($c) => $this->formatCountry($c))->values();
    }

    public function search(string $query)
    {
        if (trim($query) === '') {
            return $this->getCountries();
        }

        $countries = Country::where('name', 'LIKE', "%{$query}%")
            ->orWhere('official_name', 'LIKE', "%{$query}%")
            ->orWhere('code2', 'LIKE', "%{$query}%")
            ->orWhere('code3', 'LIKE', "%{$query}%")
            ->orderBy('name')
            ->get();

        return $countries->map(fn($c) => $this->formatCountry($c))->values();
    }

    public function getByName(string $name)
    {
        $query = Country::query();

        if (is_numeric($name)) {
            $country = $query->find((int)$name);
            return $country ? $this->formatCountry($country) : null;
        }

        // Exact match first on name, official_name, code2, code3
        $exact = (clone $query)->where(function ($w) use ($name) {
            $w->where('name', $name)
              ->orWhere('official_name', $name)
              ->orWhere('code2', strtoupper($name))
              ->orWhere('code3', strtoupper($name));
        })->first();

        if ($exact) {
            return $this->formatCountry($exact);
        }

        // Fallback to substring LIKE match
        $like = (clone $query)->where(function ($w) use ($name) {
            $w->where('name', 'LIKE', "%{$name}%")
              ->orWhere('official_name', 'LIKE', "%{$name}%");
        })->first();

        return $like ? $this->formatCountry($like) : null;
    }

    public function getByRegion(string $region)
    {
        return $this->getCountries(['q' => $region]);
    }

    public function getCountryByCode(string $code): ?array
    {
        $country = Country::where('code2', strtoupper($code))
            ->orWhere('code3', strtoupper($code))
            ->first();

        return $country ? $this->formatCountry($country) : null;
    }
}