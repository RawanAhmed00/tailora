<?php

namespace App\Repo\Class;

use App\Repo\Interfaces\ReviewRepositoryInterface;
use App\Models\ReviewsManagement;
use App\Models\Trip;
use App\Models\TourGuideRequest;
use App\Models\Booking;

class ReviewRepo implements ReviewRepositoryInterface
{
    public function getAllReviews()
    {
        return ReviewsManagement::with([
            'user:id,name,email',
            'trip.country',
            'tourGuide:id,name,email',
        ])->latest()->get();
    }

    public function approveReview(int $id)
    {
        $review = ReviewsManagement::findOrFail($id);
        $review->update(['status' => 'approved']);
        return $review;
    }

    public function rejectReview(int $id)
    {
        $review = ReviewsManagement::findOrFail($id);
        $review->update(['status' => 'rejected']);
        return $review;
    }

    public function deleteReview(int $id)
    {
        $review = ReviewsManagement::findOrFail($id);
        $review->delete();
        return true;
    }

    public function createOrUpdateReview(array $data)
    {
        return ReviewsManagement::updateOrCreate(
            [
                'user_id' => $data['user_id'],
                'trip_id' => $data['trip_id'],
                'review_type' => $data['review_type'],
            ],
            [
                'tour_guide_id' => $data['tour_guide_id'] ?? null,
                'rating' => $data['rating'],
                'comment' => $data['comment'],
                'status' => 'pending',
            ]
        );
    }

    public function getTripReviewInfo(int $tripId, int $userId)
    {
        $trip = Trip::where('id', $tripId)
            ->where('user_id', $userId)
            ->with(['country', 'tripDays.city'])
            ->first();

        if (!$trip) {
            return null;
        }

        // Check if there is an accepted tour guide request
        $guideRequest = TourGuideRequest::where('trip_id', $trip->id)
            ->where('status', 'accepted')
            ->with('tourGuide:id,name,email')
            ->first();

        // Check if booking has an assigned tour guide
        $booking = Booking::where('trip_id', $trip->id)
            ->whereNotNull('tour_guide_id')
            ->with('tourGuide:id,name,email')
            ->first();

        $assignedTourGuide = $guideRequest?->tourGuide ?? $booking?->tourGuide;

        // Existing reviews by this user for this trip
        $tripReview = ReviewsManagement::where('user_id', $userId)
            ->where('trip_id', $trip->id)
            ->where('review_type', 'trip')
            ->first();

        $tourGuideReview = ReviewsManagement::where('user_id', $userId)
            ->where('trip_id', $trip->id)
            ->where('review_type', 'tour_guide')
            ->first();

        return [
            'trip' => $trip,
            'has_assigned_tour_guide' => (bool) $assignedTourGuide,
            'assigned_tour_guide' => $assignedTourGuide,
            'existing_trip_review' => $tripReview,
            'existing_tour_guide_review' => $tourGuideReview,
        ];
    }
}
