<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ITourGuideRequestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourGuideRequestController extends Controller
{
    protected ITourGuideRequestService $tourGuideRequestService;

    public function __construct(
        ITourGuideRequestService $tourGuideRequestService
    ) {
        $this->tourGuideRequestService = $tourGuideRequestService;
    }

    /**
     * Get all requests assigned to the authenticated tour guide.
     */
    public function index(Request $request): JsonResponse
    {
        $tourGuideId = auth()->id();

        $requests = $this->tourGuideRequestService
            ->getTourGuideRequests($tourGuideId);

        return response()->json([
            'message' => 'Tour guide requests retrieved successfully.',
            'data' => $requests,
        ]);
    }

    /**
     * Get a specific request.
     */
    public function show(
        Request $request,
        int $id
    ): JsonResponse {
        $tourGuideId = auth()->id();

        $tourGuideRequest = $this->tourGuideRequestService
            ->getTourGuideRequestById(
                $tourGuideId,
                $id
            );

        return response()->json([
            'message' => 'Tour guide request retrieved successfully.',
            'data' => $tourGuideRequest,
        ]);
    }

    /**
     * Accept a tour guide request.
     */
    public function accept(
        Request $request,
        int $id
    ): JsonResponse {
        $tourGuideId = auth()->id();

        $tourGuideRequest = $this->tourGuideRequestService
            ->acceptRequest(
                $tourGuideId,
                $id
            );

        return response()->json([
            'message' => 'Tour guide request accepted successfully.',
            'data' => $tourGuideRequest,
        ]);
    }

    /**
     * Reject a tour guide request.
     */
    public function reject(
        Request $request,
        int $id
    ): JsonResponse {
        $tourGuideId = auth()->id();

        $tourGuideRequest = $this->tourGuideRequestService
            ->rejectRequest(
                $tourGuideId,
                $id
            );

        return response()->json([
            'message' => 'Tour guide request rejected successfully.',
            'data' => $tourGuideRequest,
        ]);
    }
}
