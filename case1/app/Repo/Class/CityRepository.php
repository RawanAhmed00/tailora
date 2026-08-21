<?php

namespace App\Repo\Class;

use App\Models\City;
use App\Repo\Interfaces\ICityRepository;
use Illuminate\Http\Request;

class CityRepository implements ICityRepository
{
    public function getAll()
    {
        return City::with(['country', 'attractions'])->get();
    }

    public function paginate(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        return City::with(['country', 'attractions'])
            ->paginate($perPage);
    }

    public function getById(int $id): ?City
    {
        return City::with(['country', 'attractions'])
            ->find($id);
    }

    public function create(array $data): City
    {
        return City::create($data);
    }

    public function update(int $id, array $data): ?City
    {
        $city = City::find($id);
        
        if (!$city) {
            return null;
        }

        $city->update($data);

        return $city->fresh()->load(['country', 'attractions']);
    }

    public function delete(int $id): bool
    {
        $city = City::find($id);

        if (!$city) {
            return false;
        }

        return $city->delete();
    }
}