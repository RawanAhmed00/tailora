<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Http\Requests\AirportSearchRequest;
use App\Http\Requests\BookingLinksRequest;
use App\Http\Requests\FlexibleFlightRequest;
use App\Http\Requests\OneWayFlightRequest;
use App\Http\Requests\RoundTripFlightRequest;
use App\Http\Requests\SelectFlightRequest;
use App\Services\Interfaces\IFlightService;
use Illuminate\Http\JsonResponse;
use Throwable;

class FlightController extends Controller
{
    protected IFlightService $flightService;

    public function __construct(IFlightService $flightService)
    {
        $this->flightService = $flightService;
    }

    /**
     * Search airports.
     *
     * GET /api/flights/airports
     */
    public function searchAirports(AirportSearchRequest $request): JsonResponse
    {
        try {
            $result = $this->flightService->searchAirports(
                $request->validated('q'),
                $request->validated('limit', 10)
            );

            return response()->json([
                'success' => true,
                'message' => 'Airports retrieved successfully.',
                'data' => $result,
            ], 200);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve airports.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search one-way flights.
     *
     * POST /api/flights/one-way
     */
    public function searchOneWay(OneWayFlightRequest $request): JsonResponse
    {
        try {
            $result = $this->flightService->searchOneWay(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'One-way flights retrieved successfully.',
                'data' => $result,
            ], 200);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to search one-way flights.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search round-trip flights.
     *
     * POST /api/flights/round-trip
     */
    public function searchRoundTrip(RoundTripFlightRequest $request): JsonResponse
    {
        try {
            $result = $this->flightService->searchRoundTrip(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Round-trip flights retrieved successfully.',
                'data' => $result,
            ], 200);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to search round-trip flights.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search flexible / multi-city flights.
     *
     * POST /api/flights/search
     */
    public function searchFlexible(FlexibleFlightRequest $request): JsonResponse
    {
        try {
            $result = $this->flightService->searchFlexible(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Flexible flight search completed successfully.',
                'data' => $result,
            ], 200);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to search flexible flights.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get booking links for a flight itinerary.
     *
     * POST /api/flights/booking-links
     */
    public function getBookingLinks(BookingLinksRequest $request): JsonResponse
    {
        try {
            $result = $this->flightService->getBookingLinks(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Booking links retrieved successfully.',
                'data' => $result,
            ], 200);

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve booking links.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

public function selectFlight(SelectFlightRequest $request): JsonResponse
{
    try {
        $flight = $this->flightService->selectFlight(
            $request->validated()['ignav_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Flight selected successfully.',
            'data' => $flight,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to select flight.',
            'error' => $e->getMessage(),
        ], 400);
    }
}

}
