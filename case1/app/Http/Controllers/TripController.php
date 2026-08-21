<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTripRequest;
use App\Http\Requests\SelectCitiesRequest;
use App\Http\Requests\SelectTripCitiesRequest;
use App\Http\Requests\SelectTripDayAttractionsRequest;
use App\Http\Requests\UpdateTripDayCityRequest;
use App\Services\Interfaces\ITripService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripController extends Controller
{
    protected ITripService $tripService;

    public function __construct(
        ITripService $tripService
    ) {
        $this->tripService = $tripService;
    }

    public function store(
        CreateTripRequest $request
    ): JsonResponse {

        $trip = $this->tripService->createTrip(
            auth()->id(),
            $request->validated()
        );

        return response()->json([
            'message' => 'Trip created successfully.',
            'data' => $trip->load([
                'country',
            ]),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $trip = $this->tripService->getTrip(
            $id,
            auth()->id()
        );

        if (!$trip) {
            return response()->json([
                'message' => 'Trip not found.',
            ], 404);
        }

        return response()->json([
            'data' => $trip,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = $request->integer(
            'per_page',
            10
        );

        $perPage = min($perPage, 50);

        $trips = $this->tripService->getUserTrips(
            auth()->id(),
            $perPage
        );

        return response()->json([
            'data' => $trips,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->tripService->deleteTrip(
            $id,
            auth()->id()
        );

        if (!$deleted) {
            return response()->json([
                'message' => 'Trip not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Trip deleted successfully.',
        ]);
    }
public function selectCities(
    SelectTripCitiesRequest $request,
    int $id
): JsonResponse {

    try {

        $data = $request->validated();

        $trip = $this->tripService->selectCities(
            $id,
            auth()->id(),
            $data['days']
        );

        if (!$trip) {
            return response()->json([
                'message' => 'Trip not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'message' => 'Cities selected successfully.',
            'data' => $trip,
        ], 200);

    } catch (\InvalidArgumentException $e) {

        return response()->json([
            'message' => $e->getMessage(),
        ], 422);

    } catch (\Throwable $e) {

        return response()->json([
            'message' => $e->getMessage(),
        ], 500);
    }
}
public function updateTripDayCity(
    UpdateTripDayCityRequest $request,
    int $id
): JsonResponse {

    try {

        $trip = $this->tripService->updateTripDayCity(
            $id,
            auth()->id(),
            $request->validated('city_id')
        );

        if (!$trip) {
            return response()->json([
                'message' => 'Trip day not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'message' => 'Trip day city updated successfully.',
            'data' => $trip,
        ], 200);

    } catch (\InvalidArgumentException $e) {

        return response()->json([
            'message' => $e->getMessage(),
        ], 422);
    }
}
public function getTripDayAttractions(
    string $tripDayId
): JsonResponse {

    try {

        $attractions = $this->tripService
            ->getTripDayAttractions(
                (int) $tripDayId,
                auth()->id()
            );

        if ($attractions === null) {
            return response()->json([
                'message' => 'Trip day not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'message' => 'Attractions retrieved successfully.',
            'data' => $attractions,
        ], 200);

    } catch (\InvalidArgumentException $e) {

        return response()->json([
            'message' => $e->getMessage(),
        ], 422);
    }
}
public function selectTripDayAttractions(
    SelectTripDayAttractionsRequest $request,
    string $tripDayId
): JsonResponse {

    try {

        $tripDay = $this->tripService
            ->selectTripDayAttractions(
                (int) $tripDayId,
                auth()->id(),
                $request->validated()['attraction_ids']
            );

        if (!$tripDay) {
            return response()->json([
                'message' => 'Trip day not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'message' => 'Attractions selected successfully.',
            'data' => $tripDay,
        ], 200);

    } catch (\InvalidArgumentException $e) {

        return response()->json([
            'message' => $e->getMessage(),
        ], 422);

    } catch (\Throwable $e) {

        return response()->json([
            'message' => $e->getMessage(),
        ], 500);
    }
}
public function getFullTrip(
    string $id
): JsonResponse {

    $trip = $this->tripService->getFullTrip(
        (int) $id,
        auth()->id()
    );

    if (!$trip) {
        return response()->json([
            'message' => 'Trip not found or unauthorized.',
        ], 404);
    }

    return response()->json([
        'message' => 'Full trip retrieved successfully.',
        'data' => $trip,
    ], 200);
}
}