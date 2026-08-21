<?php

namespace App\Repo\Class;

use App\Models\Availability;

use App\Repo\Interfaces\IAvailabilityRepo;

class AvailabilityClass implements IAvailabilityRepo
{
    public function getAllAvailabilities(int $perPage = 50)
    {
        $query = Availability::query();
        if (auth()->check() && auth()->user()->role === 't_guide') {
            $query->where('tour_guide_id', auth()->id());
        }
        return $query->orderBy('date', 'asc')->orderBy('start_time', 'asc')->paginate($perPage);
    }
    public function createAvailability(array $data)
    {
        return Availability::create($data);
    
    }
    public function updateAvailability(int $id, array $data)
    {
         $availability = Availability::findOrFail($id);
         $availability->update($data);
         return $availability;
    }
    public function deleteAvailability(int $id)
    {
        return Availability::destroy($id);
    }
}