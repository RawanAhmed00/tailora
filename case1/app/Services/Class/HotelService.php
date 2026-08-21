<?php

namespace App\Services\Class;

use App\Repo\Interfaces\HotelRepositoryInterface;
use App\Models\Hotel;

class HotelService
{
    protected HotelRepositoryInterface $hotelRepository;

    public function __construct(HotelRepositoryInterface $hotelRepository)
    {
        $this->hotelRepository = $hotelRepository;
    }

    public function getAll()
    {
        return $this->hotelRepository->getAll();
    }

    public function getById(int $id): ?Hotel
    {
        return $this->hotelRepository->getById($id);
    }

    public function create(array $data): Hotel
    {
        return $this->hotelRepository->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->hotelRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->hotelRepository->delete($id);
    }
}