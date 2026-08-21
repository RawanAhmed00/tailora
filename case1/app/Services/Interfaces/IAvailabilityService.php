<?php

namespace App\Services\Interfaces;

interface IAvailabilityService
{
    public function getAllAvailabilities(int $perPage =10);

    public function createAvailable(array $data);

    public function updateAvailable(int $id, array $data);

    public function deleteAvailable(int $id);
}