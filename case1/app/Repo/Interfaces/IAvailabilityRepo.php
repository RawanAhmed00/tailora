<?php

namespace App\Repo\Interfaces;

interface IAvailabilityRepo
{
    public function getAllAvailabilities(int $perPage = 10);

    public function createAvailability(array $data);

    public function updateAvailability(int $id, array $data);

    public function deleteAvailability(int $id);
}