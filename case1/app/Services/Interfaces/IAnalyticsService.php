<?php

namespace App\Services\Interfaces;

interface IAnalyticsService
{
    public function dashboard();

    public function getStatistics(int $userId): array;

    public function getSavedTrips(int $userId);

    public function getFavorites(int $userId);

    public function getBookingHistory(int $userId);
    public function getTourGuideDashboard(int $tourGuideId): array;
}