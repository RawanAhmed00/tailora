<?php

namespace App\Services\Class;
use App\Services\Interfaces\IAvailabilityService;
use App\Repo\Interfaces\IAvailabilityRepo;

class AvailabilityService implements IAvailabilityService
{
    private $availabilityRepo;

    public function __construct(IAvailabilityRepo $availabilityRepo)
    {
        $this->availabilityRepo = $availabilityRepo;
    }
    public function getAllAvailabilities(int $perPage =10)
    {
        return $this->availabilityRepo->getAllAvailabilities($perPage);
    }
    public function createAvailable(array $data)
    {
        return $this->availabilityRepo->createAvailability($data);
    }
    public function updateAvailable(int $id, array $data)
    {
        return $this->availabilityRepo->updateAvailability($id, $data);
    }
    public function deleteAvailable(int $id)
    {
        return $this->availabilityRepo->deleteAvailability($id);
    }
    
}