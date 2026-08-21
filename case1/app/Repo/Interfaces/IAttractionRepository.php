<?php

namespace App\Repo\Interfaces;

use App\Models\Attraction;

interface IAttractionRepository
{
    public function getAll(int $perPage = 10);

    public function getById(int $id): ?Attraction;

    public function create(array $data): Attraction;

    public function update(int $id, array $data): ?Attraction;

    public function delete(int $id): bool;
}
