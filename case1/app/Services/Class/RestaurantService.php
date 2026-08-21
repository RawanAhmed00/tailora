<?php

namespace App\Services\Class;

use App\Repo\Interfaces\RestaurantRepositoryInterface;

class RestaurantService
{
    protected RestaurantRepositoryInterface $restaurantRepository;

    public function __construct(RestaurantRepositoryInterface $restaurantRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
    }

  public function getAll(array $filters = [])
{
    return $this->restaurantRepository->getAll($filters);
}

    public function getById(int $id)
    {
        return $this->restaurantRepository->getById($id);
    }

    public function create(array $data)
    {
        return $this->restaurantRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->restaurantRepository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->restaurantRepository->delete($id);
    }
}