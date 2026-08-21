<?php

namespace App\Services\Interfaces;

use App\Models\Flight;
interface IFlightService
{
    /**
     * Search airports by name, city, country or IATA code.
     */
    public function searchAirports(string $query, int $limit = 10): array;

    /**
     * Search one-way flights.
     */
    public function searchOneWay(array $data): array;

    /**
     * Search round-trip flights.
     */
    public function searchRoundTrip(array $data): array;

    /**
     * Search flexible / multi-city flights.
     */
    public function searchFlexible(array $data): array;

    /**
     * Get booking links for a selected itinerary.
     */
    public function getBookingLinks(array $data): array;

  public function selectFlight(string $ignavId): Flight;
}