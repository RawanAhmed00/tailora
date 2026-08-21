<?php

namespace App\Services\Class;

use App\Repo\Interfaces\ReviewRepositoryInterface;

class ReviewService implements ReviewRepositoryInterface
{
    public function __construct(
        protected ReviewRepositoryInterface $reviewRepository
    ) {}

    public function approveReview(int $id)
    {
        return $this->reviewRepository->approveReview($id);
    }

    public function rejectReview(int $id)
    {
        return $this->reviewRepository->rejectReview($id);
    }

    public function deleteReview(int $id)
    {
        return $this->reviewRepository->deleteReview($id);
    }

    public function getAllReviews()
    {
        return $this->reviewRepository->getAllReviews();
    }

    public function createOrUpdateReview(array $data)
    {
        return $this->reviewRepository->createOrUpdateReview($data);
    }

    public function getTripReviewInfo(int $tripId, int $userId)
    {
        return $this->reviewRepository->getTripReviewInfo($tripId, $userId);
    }
}
