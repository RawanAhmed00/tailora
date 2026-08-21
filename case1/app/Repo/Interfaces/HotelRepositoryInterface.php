<?php

namespace App\Repo\Interfaces;

use App\Models\Hotel;

interface HotelRepositoryInterface
{
    public function getAll(array $filters = []);

    public function getById(int $id): ?Hotel;

    public function create(array $data): Hotel;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
