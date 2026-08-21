<?php

namespace App\Services\Interfaces;

use App\Models\Favorite;

interface FavoriteServiceInterface
{
    public function getUserFavorites(int $userId);

    public function addFavorite(
        int $userId,
        string $type,
        int $id
    ): Favorite;

    public function removeFavorite(
        int $userId,
        string $type,
        int $id
    ): bool;
}