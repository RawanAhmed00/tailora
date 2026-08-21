<?php

namespace App\Repo\Interfaces;

interface TourGuideRepositoryInterface
{
    public function getSchedule();
    public function getEarnings();
    public function getEarningsHistory();

    public function getReviews();

    public function getRating();
}