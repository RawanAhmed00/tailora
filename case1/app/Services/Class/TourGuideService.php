<?php

namespace App\Services\Class;

use App\Repo\Interfaces\TourGuideRepositoryInterface;

class TourGuideService implements TourGuideRepositoryInterface
{
    protected TourGuideRepositoryInterface $tourGuideRepository;

    public function __construct(TourGuideRepositoryInterface $tourGuideRepository)
    {
        $this->tourGuideRepository = $tourGuideRepository;
    }

    public function getSchedule()
    {
        return $this->tourGuideRepository->getSchedule();
    }

    public function getEarnings()
    {
        return $this->tourGuideRepository->getEarnings();
    }

    public function getEarningsHistory()
    {
        return $this->tourGuideRepository->getEarningsHistory();
    }

    public function getReviews()
{
    return $this->tourGuideRepository->getReviews();
}

public function getRating()
{
    return $this->tourGuideRepository->getRating();
}


}