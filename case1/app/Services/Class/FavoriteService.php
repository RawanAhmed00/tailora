<?php

namespace App\Services\Class;

use App\Models\Attraction;
use App\Models\City;
use App\Models\Country;
use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Repo\Interfaces\FavoriteRepositoryInterface;
use App\Services\Interfaces\FavoriteServiceInterface;
use InvalidArgumentException;

class FavoriteService implements FavoriteServiceInterface
{
    protected FavoriteRepositoryInterface $favoriteRepository;

    public function __construct(FavoriteRepositoryInterface $favoriteRepository)
    {
        $this->favoriteRepository = $favoriteRepository;
    }

    public function getUserFavorites(int $userId)
    {
        return $this->favoriteRepository->getUserFavorites($userId);
    }

    public function addFavorite(
        int $userId,
        string $type,
        int $id
    ): Favorite {

        $favoritableType = $this->resolveType($type);

        return $this->favoriteRepository->addFavorite(
            $userId,
            $favoritableType,
            $id
        );
    }

    public function removeFavorite(
        int $userId,
        string $type,
        int $id
    ): bool {

        $favoritableType = $this->resolveType($type);

        return $this->favoriteRepository->removeFavorite(
            $userId,
            $favoritableType,
            $id
        );
    }

    private function resolveType(string $type): string
    {
        return match (strtolower($type)) {
            'country' => Country::class,
            'city' => City::class,
            'hotel' => Hotel::class,
            'restaurant' => Restaurant::class,
            'attraction' => Attraction::class,

            default => throw new InvalidArgumentException('Invalid favorite type'),
        };
    }
}