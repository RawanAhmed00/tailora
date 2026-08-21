<?php

namespace App\Services\Class;

use App\Repo\Interfaces\ICategoryRepository;
use App\Services\Interfaces\ICategoryService;
use Illuminate\Support\Facades\Cache;

class CategoryService implements ICategoryService
{
    protected ICategoryRepository $categoryRepository;

    public function __construct(ICategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAll()
    {
        return Cache::remember('categories', now()->addHour(), function () {
            return $this->categoryRepository->getAll();
        });
    }

    public function getById(int $id)
    {
        return $this->categoryRepository->getById($id);
    }

    public function create(array $data)
    {
        Cache::forget('categories');

        return $this->categoryRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        Cache::forget('categories');

        return $this->categoryRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        Cache::forget('categories');

        return $this->categoryRepository->delete($id);
    }
}