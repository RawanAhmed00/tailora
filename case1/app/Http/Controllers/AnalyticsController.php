<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\IAnalyticsService;

class AnalyticsController extends Controller
{
    protected IAnalyticsService $analyticsService;

    public function __construct(
        IAnalyticsService $analyticsService
    ) {
        $this->analyticsService = $analyticsService;
    }

    public function dashboard()
    {
        $data = $this->analyticsService->dashboard();

        return response()->json([
            'message' => 'Dashboard retrieved successfully',
            'data' => $data,
        ]);
    }

    public function statistics()
    {
        $data = $this->analyticsService->getStatistics(
            auth()->id()
        );

        return response()->json([
            'message' => 'Statistics retrieved successfully',
            'data' => $data,
        ]);
    }

    public function savedTrips()
    {
        $data = $this->analyticsService->getSavedTrips(
            auth()->id()
        );

        return response()->json([
            'message' => 'Saved trips retrieved successfully',
            'data' => $data,
        ]);
    }

    public function favorites()
    {
        $data = $this->analyticsService->getFavorites(
            auth()->id()
        );

        return response()->json([
            'message' => 'Favorite destinations retrieved successfully',
            'data' => $data,
        ]);
    }

    public function bookingHistory()
    {
        $data = $this->analyticsService->getBookingHistory(
            auth()->id()
        );

        return response()->json([
            'message' => 'Booking history retrieved successfully',
            'data' => $data,
        ]);
    }

    public function tourGuideDashboard()
{
    $dashboard = $this->analyticsService->getTourGuideDashboard(
        auth()->id()
    );

    return response()->json($dashboard);
}
}