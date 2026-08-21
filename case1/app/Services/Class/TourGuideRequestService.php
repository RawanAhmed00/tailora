<?php

namespace App\Services\Class;

use App\Repo\Interfaces\ITourGuideRequestRepository;
use App\Services\Interfaces\ITourGuideRequestService;

class TourGuideRequestService implements ITourGuideRequestService
{
    protected ITourGuideRequestRepository $tourGuideRequestRepository;

    public function __construct(
        ITourGuideRequestRepository $tourGuideRequestRepository
    ) {
        $this->tourGuideRequestRepository = $tourGuideRequestRepository;
    }

    public function getTourGuideRequests(int $tourGuideId)
    {
        return $this->tourGuideRequestRepository
            ->getTourGuideRequests($tourGuideId);
    }

    public function getTourGuideRequestById(
        int $tourGuideId,
        int $requestId
    ) {
        return $this->tourGuideRequestRepository
            ->getTourGuideRequestById(
                $tourGuideId,
                $requestId
            );
    }

    public function acceptRequest(
        int $tourGuideId,
        int $requestId
    ) {
        return $this->tourGuideRequestRepository
            ->acceptRequest(
                $tourGuideId,
                $requestId
            );
    }

    public function rejectRequest(
        int $tourGuideId,
        int $requestId
    ) {
        return $this->tourGuideRequestRepository
            ->rejectRequest(
                $tourGuideId,
                $requestId
            );
    }
}
