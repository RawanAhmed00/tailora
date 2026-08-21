<?php

namespace App\Services\Interfaces;

interface ITourGuideRequestService
{
    public function getTourGuideRequests(int $tourGuideId);

    public function getTourGuideRequestById(
        int $tourGuideId,
        int $requestId
    );

    public function acceptRequest(
        int $tourGuideId,
        int $requestId
    );

    public function rejectRequest(
        int $tourGuideId,
        int $requestId
    );
}
