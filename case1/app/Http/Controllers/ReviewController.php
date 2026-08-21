<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TourGuideRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Services\Class\ReviewService;
use Illuminate\Http\JsonResponse;

class ReviewController extends Controller
{
    protected ReviewService $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    public function index(): JsonResponse
    {
        return response()->json(
            $this->reviewService->getAllReviews()
        );
    }

    public function getTripReviewInfo(int $tripId): JsonResponse
    {
        $info = $this->reviewService->getTripReviewInfo($tripId, auth()->id());

        if (!$info) {
            return response()->json([
                'message' => 'Trip not found or unauthorized.'
            ], 404);
        }

        return response()->json([
            'data' => $info
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'trip_id' => 'required|integer|exists:trips,id',
            'review_type' => 'required|string|in:trip,tour_guide',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $userId = auth()->id();

        // 1. Verify trip exists and belongs to authenticated user
        $trip = Trip::where('id', $validated['trip_id'])
            ->where('user_id', $userId)
            ->first();

        if (!$trip) {
            return response()->json([
                'message' => 'Trip not found or unauthorized.'
            ], 403);
        }

        $tourGuideId = null;

        // 2. Validate Tour Guide Review condition & fetch verified tour guide ID
        if ($validated['review_type'] === 'tour_guide') {
            $acceptedRequest = TourGuideRequest::where('trip_id', $trip->id)
                ->where('status', 'accepted')
                ->first();

            $booking = Booking::where('trip_id', $trip->id)
                ->whereNotNull('tour_guide_id')
                ->first();

            $assignedGuideId = $acceptedRequest?->tour_guide_id ?? $booking?->tour_guide_id;

            if (!$assignedGuideId) {
                return response()->json([
                    'message' => 'Cannot submit a tour guide review: No tour guide is assigned to this trip.'
                ], 422);
            }

            $tourGuideId = $assignedGuideId;
        }

        // 3. Create or update review in database
        $review = $this->reviewService->createOrUpdateReview([
            'user_id' => $userId,
            'trip_id' => $trip->id,
            'tour_guide_id' => $tourGuideId,
            'review_type' => $validated['review_type'],
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'message' => 'Review submitted successfully.',
            'data' => $review->load(['user:id,name,email', 'trip.country', 'tourGuide:id,name,email'])
        ], 201);
    }

    public function approve($id): JsonResponse
    {
        $this->reviewService->approveReview((int) $id);

        return response()->json([
            'message' => 'Review approved successfully'
        ]);
    }

    public function reject($id): JsonResponse
    {
        $this->reviewService->rejectReview((int) $id);

        return response()->json([
            'message' => 'Review rejected successfully'
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->reviewService->deleteReview((int) $id);

        return response()->json([
            'message' => 'Review deleted successfully'
        ]);
    }
}
