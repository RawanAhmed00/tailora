<?php

namespace App\Repo\Class;

use App\Models\Favorite;
use App\Repo\Interfaces\FavoriteRepositoryInterface;

class FavoriteRepository implements FavoriteRepositoryInterface
{
    public function getUserFavorites(int $userId)
    {
        return Favorite::where('user_id', $userId)->get();
    }

   public function addFavorite(
    int $userId,
    string $favoritableType,
    int $favoritableId
     ): Favorite {
    return Favorite::firstOrCreate([
        'user_id' => $userId,
        'favoritable_type' => $favoritableType,
        'favoritable_id' => $favoritableId,
    ]);

    }

    public function removeFavorite(
        int $userId,
        string $favoritableType,
        int $favoritableId
    ): bool {
        return Favorite::where([
            'user_id' => $userId,
            'favoritable_type' => $favoritableType,
            'favoritable_id' => $favoritableId,
        ])->delete() > 0;
    }
}