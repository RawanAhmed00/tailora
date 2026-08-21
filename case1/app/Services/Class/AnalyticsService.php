<?php

namespace App\Services\Class;

use App\Repo\Interfaces\IAnalyticsRepository;
use App\Services\Interfaces\IAnalyticsService;

class AnalyticsService implements IAnalyticsService
{
    protected IAnalyticsRepository $analyticsRepository;

    public function __construct(
        IAnalyticsRepository $analyticsRepository
    ) {
        $this->analyticsRepository = $analyticsRepository;
    }

    public function dashboard()
    {
        return $this->analyticsRepository->dashboard();
    }

    public function getStatistics(int $userId): array
    {
        return $this->analyticsRepository->getStatistics($userId);
    }

    public function getSavedTrips(int $userId)
    {
        return $this->analyticsRepository->getSavedTrips($userId);
    }

    public function getFavorites(int $userId)
    {
        return $this->analyticsRepository->getFavorites($userId);
    }

    public function getBookingHistory(int $userId)
    {
        return $this->analyticsRepository->getBookingHistory($userId);
    }

    public function getTourGuideDashboard(int $tourGuideId): array
{
    return $this->analyticsRepository->getTourGuideDashboard($tourGuideId);
}
}