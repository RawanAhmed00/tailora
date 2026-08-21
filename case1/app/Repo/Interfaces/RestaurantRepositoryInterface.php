<?php

namespace App\Repo\Interfaces;

interface RestaurantRepositoryInterface
{
    public function getAll(array $filters = []);

    public function getById(int $id);

    public function create(array $data);

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}