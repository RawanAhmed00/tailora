<?php

namespace App\Services\Interfaces;

interface IAiService
{
   

    public function enhance(string $content);

    public function recommendations(
        int $userId,
        string $type,
        int $cityId,
        ?float $budget = null,
        int $limit = 10,
        ?string $preference = null
    );
    public function bestPlaces(string $destination,?string $travelStyle = null,?array $interests = null);
    public function travelTips(string $destination,?string $travelStyle = null,?array $interests = null);
    public function tripRecommendation(int $userId,int $tripId,string $message);
    public function travel(int $userId,string $message);
    public function generatePlans(int $userId,int $conversationId);
    public function choosePlan(int $userId,int $conversationId,int $planId);
}