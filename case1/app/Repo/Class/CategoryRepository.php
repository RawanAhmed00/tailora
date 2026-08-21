<?php

namespace App\Repo\Class;

use App\Models\Category;
use App\Repo\Interfaces\ICategoryRepository;

class CategoryRepository implements ICategoryRepository
{
    public function getAll()
    {
        return Category::all();
    }

    public function getById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): ?Category
    {
        $category = Category::find($id);

        if (!$category) {
            return null;
        }

        $category->update($data);

        return $category;
    }

    public function delete(int $id): bool
    {
        $category = Category::find($id);

        if (!$category) {
            return false;
        }

        return $category->delete();
    }
}