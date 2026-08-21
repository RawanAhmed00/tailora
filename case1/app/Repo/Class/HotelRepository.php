<?php

namespace App\Repo\Class;

use App\Models\Hotel;
use App\Repo\Interfaces\HotelRepositoryInterface;

class HotelRepository implements HotelRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        $query = Hotel::query();

        // Search by hotel name
        if (!empty($filters['search'])) {
            $query->where(
                'name',
                'like',
                '%' . $filters['search'] . '%'
            );
        }

        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->where(
                'city_id',
                $filters['city_id']
            );
        }

        // Filter by minimum rating
        if (!empty($filters['min_rating'])) {
            $query->where(
                'rating',
                '>=',
                $filters['min_rating']
            );
        }

        // Filter by maximum price
        if (!empty($filters['max_price'])) {
            $query->where(
                'price',
                '<=',
                $filters['max_price']
            );
        }

        // Sorting
        switch ($filters['sort'] ?? null) {

            case 'rating_high':
                $query->orderBy('rating', 'desc');
                break;

            case 'rating_low':
                $query->orderBy('rating', 'asc');
                break;

            case 'price_low':
                $query->orderBy('price', 'asc');
                break;

            case 'price_high':
                $query->orderBy('price', 'desc');
                break;

            case 'latest':
                $query->latest();
                break;

            case 'oldest':
                $query->oldest();
                break;

            default:
                $query->latest();
                break;
        }

        // Pagination
        return $query->paginate(
            $filters['per_page'] ?? 10
        );
    }

    public function getById(int $id): ?Hotel
    {
        return Hotel::find($id);
    }

    public function create(array $data): Hotel
    {
        return Hotel::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $hotel = Hotel::findOrFail($id);

        return $hotel->update($data);
    }

    public function delete(int $id): bool
    {
        $hotel = Hotel::findOrFail($id);

        return $hotel->delete();
    }
}
