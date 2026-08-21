<?php

namespace App\Repo\Interfaces;

use App\Models\Category;

interface ICategoryRepository
{
    public function getAll();

    public function getById(int $id): ?Category;

    public function create(array $data): Category;

    public function update(int $id, array $data): ?Category;

    public function delete(int $id): bool;
}