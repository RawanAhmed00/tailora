<?php

namespace App\Repo\Class;

use App\Models\Attraction;
use App\Repo\Interfaces\IAttractionRepository;
use Illuminate\Support\Facades\Cache;

class AttractionRepository implements IAttractionRepository
{
    
public function getAll(int $perPage = 10)
{
    $page = request()->input('page', 1);

    $cacheKey = "attractions_page_{$page}_{$perPage}";

    return Cache::remember($cacheKey, now()->addHour(), function () use ($perPage) {
        return Attraction::with(['city', 'categories'])
            ->paginate($perPage);
    });
}

    public function getById(int $id): ?Attraction
    {
        return Attraction::with(['city', 'categories'])->find($id);
    }

    public function create(array $data): Attraction
    {
        return Attraction::create($data);
    }

    public function update(int $id, array $data): ?Attraction
    {
        $attraction = Attraction::find($id);

        if (!$attraction) {
            return null;
        }

        $attraction->update($data);

        return $attraction;
    }

    public function delete(int $id): bool
    {
        $attraction = Attraction::find($id);

        if (!$attraction) {
            return false;
        }

        return $attraction->delete();
    }
}