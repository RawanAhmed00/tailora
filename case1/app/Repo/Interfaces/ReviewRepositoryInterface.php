<?php

namespace App\Repo\Interfaces;

interface ReviewRepositoryInterface
{
    public function getAllReviews();
    public function approveReview(int $id);
    public function rejectReview(int $id);
    public function deleteReview(int $id);
    public function createOrUpdateReview(array $data);
    public function getTripReviewInfo(int $tripId, int $userId);
}
