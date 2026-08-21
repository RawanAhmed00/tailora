<?php

namespace App\Repo\Class;

use App\Models\Restaurant;
use App\Repo\Interfaces\RestaurantRepositoryInterface;

class RestaurantRepository implements RestaurantRepositoryInterface
{
    public function getAll(array $filters = [])
    {
        $query = Restaurant::query();

        // Search by restaurant name
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by rating
        if (!empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }

        // Sorting
        if (!empty($filters['sort'])) {
            if ($filters['sort'] === 'rating_high') {
                $query->orderBy('rating', 'desc');
            }

            if ($filters['sort'] === 'rating_low') {
                $query->orderBy('rating', 'asc');
            }

            if ($filters['sort'] === 'latest') {
                $query->latest();
            }

            if ($filters['sort'] === 'oldest') {
                $query->oldest();
            }
        } else {
            $query->latest();
        }

        return $query->paginate(
            $filters['per_page'] ?? 10
        );
    }

    public function getById(int $id): ?Restaurant
    {
        return Restaurant::find($id);
    }

    public function create(array $data): Restaurant
    {
        return Restaurant::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $restaurant = Restaurant::findOrFail($id);

        return $restaurant->update($data);
    }

    public function delete(int $id): bool
    {
        $restaurant = Restaurant::findOrFail($id);

        return $restaurant->delete();
    }
}