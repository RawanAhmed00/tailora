<?php

namespace App\Services\Class;

use App\Models\Flight;
use App\Services\Interfaces\IFlightService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FlightService implements IFlightService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.ignav.base_url');
        $this->apiKey = config('services.ignav.api_key');
    }

    /**
     * Send request to Ignav API.
     */
    protected function request()
    {
        return Http::withHeaders([
            'X-Api-Key' => $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Search airports.
     */
    public function searchAirports(string $query, int $limit = 10): array
    {
        $response = $this->request()
            ->get($this->baseUrl . '/airports', [
                'q' => $query,
                'limit' => $limit,
            ]);

        $response->throw();

        return $response->json();
    }

    /**
     * Search one-way flights.
     */
    public function searchOneWay(array $data): array
    {
        $response = $this->request()
            ->post(
                $this->baseUrl . '/fares/one-way',
                $data
            );

        $response->throw();

        $result = $response->json();

        /*
        |--------------------------------------------------------------------------
        | Store flights in cache
        |--------------------------------------------------------------------------
        */

        foreach ($result['itineraries'] ?? [] as $itinerary) {

            if (!empty($itinerary['ignav_id'])) {

                Cache::put(
                    'ignav_flight_' . $itinerary['ignav_id'],
                    [
                        'ignav_id' => $itinerary['ignav_id'],

                        'origin' =>
                            $result['origin']
                            ?? $data['origin'],

                        'destination' =>
                            $result['destination']
                            ?? $data['destination'],

                        'departure_date' =>
                            $result['departure_date']
                            ?? $data['departure_date'],

                        'return_date' => null,

                        'price' =>
                            $itinerary['price']
                            ?? null,

                        'outbound' =>
                            $itinerary['outbound']
                            ?? null,

                        'inbound' => null,

                        'cabin_class' =>
                            $itinerary['cabin_class']
                            ?? null,

                        'requires_self_transfer' =>
                            $itinerary['requires_self_transfer']
                            ?? false,
                    ],
                    now()->addMinutes(30)
                );
            }
        }

        return $result;
    }

    /**
     * Search round-trip flights.
     */
    public function searchRoundTrip(array $data): array
    {
        $response = $this->request()
            ->post(
                $this->baseUrl . '/fares/round-trip',
                $data
            );

        $response->throw();

        $result = $response->json();

        /*
        |--------------------------------------------------------------------------
        | Store each flight in cache
        |--------------------------------------------------------------------------
        */

        foreach ($result['itineraries'] ?? [] as $itinerary) {

            if (!empty($itinerary['ignav_id'])) {

                Cache::put(
                    'ignav_flight_' . $itinerary['ignav_id'],
                    [
                        'ignav_id' => $itinerary['ignav_id'],

                        'origin' =>
                            $result['origin']
                            ?? $data['origin'],

                        'destination' =>
                            $result['destination']
                            ?? $data['destination'],

                        'departure_date' =>
                            $result['departure_date']
                            ?? $data['departure_date'],

                        'return_date' =>
                            $result['return_date']
                            ?? $data['return_date'],

                        'price' =>
                            $itinerary['price']
                            ?? null,

                        'outbound' =>
                            $itinerary['outbound']
                            ?? null,

                        'inbound' =>
                            $itinerary['inbound']
                            ?? null,

                        'cabin_class' =>
                            $itinerary['cabin_class']
                            ?? null,

                        'requires_self_transfer' =>
                            $itinerary['requires_self_transfer']
                            ?? false,

                        'booking_url' =>
                            $itinerary['booking_url']
                            ?? null,
                    ],
                    now()->addMinutes(30)
                );
            }
        }

        return $result;
    }

    /**
     * Search flexible / multi-city flights.
     */
    public function searchFlexible(array $data): array
    {
        $response = $this->request()
            ->post(
                $this->baseUrl . '/fares/search',
                $data
            );

        $response->throw();

        return $response->json();
    }

    /**
     * Get booking links.
     */
    public function getBookingLinks(array $data): array
    {
        $response = $this->request()
            ->post(
                $this->baseUrl . '/fares/booking-links',
                $data
            );

        $response->throw();

        return $response->json();
    }

    /**
     * Select flight.
     *
     * User sends only ignav_id.
     */
public function selectFlight(string $ignavId): Flight
{
    /*
    |--------------------------------------------------------------------------
    | Get authenticated user
    |--------------------------------------------------------------------------
    */

    $userId = auth()->id();

    if (!$userId) {
        throw new \Exception('Unauthenticated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Get selected flight from cache
    |--------------------------------------------------------------------------
    */

    $data = Cache::get(
        'ignav_flight_' . $ignavId
    );

    if (!$data) {
        throw new \Exception(
            'Flight not found or search result expired.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validate price
    |--------------------------------------------------------------------------
    */

    if (
        !isset($data['price']) ||
        !isset($data['price']['amount'])
    ) {
        throw new \Exception(
            'Flight price is not available.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Outbound first segment
    |--------------------------------------------------------------------------
    */

    $outboundSegment =
        $data['outbound']['segments'][0]
        ?? null;

    /*
    |--------------------------------------------------------------------------
    | Inbound first segment
    |--------------------------------------------------------------------------
    */

    $inboundSegment =
        $data['inbound']['segments'][0]
        ?? null;

    /*
    |--------------------------------------------------------------------------
    | Calculate stops
    |--------------------------------------------------------------------------
    */

    $outboundStops =
        max(
            count($data['outbound']['segments'] ?? []) - 1,
            0
        );

    $inboundStops =
        max(
            count($data['inbound']['segments'] ?? []) - 1,
            0
        );

    /*
    |--------------------------------------------------------------------------
    | Save selected flight for authenticated user
    |--------------------------------------------------------------------------
    */

    return Flight::updateOrCreate(
        [
            'user_id' => $userId,
            'ignav_id' => $ignavId,
        ],
        [
            'origin' =>
                $data['origin'],

            'destination' =>
                $data['destination'],

            'departure_date' =>
                $data['departure_date'],

            'return_date' =>
                $data['return_date'] ?? null,

            /*
            |--------------------------------------------------------------------------
            | Airline
            |--------------------------------------------------------------------------
            */

            'airline' =>
                $data['outbound']['carrier']
                ?? null,

            /*
            |--------------------------------------------------------------------------
            | Outbound
            |--------------------------------------------------------------------------
            */

            'outbound_carrier_code' =>
                $outboundSegment['marketing_carrier_code']
                ?? null,

            'outbound_flight_number' =>
                $outboundSegment['flight_number']
                ?? null,

            /*
            |--------------------------------------------------------------------------
            | Inbound
            |--------------------------------------------------------------------------
            */

            'inbound_carrier_code' =>
                $inboundSegment['marketing_carrier_code']
                ?? null,

            'inbound_flight_number' =>
                $inboundSegment['flight_number']
                ?? null,

            /*
            |--------------------------------------------------------------------------
            | Price
            |--------------------------------------------------------------------------
            */

            'price' =>
                $data['price']['amount'],

            'currency' =>
                $data['price']['currency']
                ?? 'USD',

            /*
            |--------------------------------------------------------------------------
            | Cabin
            |--------------------------------------------------------------------------
            */

            'cabin_class' =>
                $data['cabin_class']
                ?? null,

            /*
            |--------------------------------------------------------------------------
            | Stops
            |--------------------------------------------------------------------------
            */

            'stops' =>
                max(
                    $outboundStops,
                    $inboundStops
                ),

            /*
            |--------------------------------------------------------------------------
            | Booking URL
            |--------------------------------------------------------------------------
            */

            'booking_url' =>
                $data['booking_url']
                ?? null,
        ]
    );
}

}