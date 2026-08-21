<?php

namespace App\Repo\Interfaces;

use App\Models\Favorite;

interface FavoriteRepositoryInterface
{
    public function getUserFavorites(int $userId);

    public function addFavorite(
        int $userId,
        string $favoritableType,
        int $favoritableId
    ): Favorite;

    public function removeFavorite(
        int $userId,
        string $favoritableType,
        int $favoritableId
    ): bool;
}