<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ITripDayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TripDayController extends Controller
{
    protected ITripDayService $tripDayService;

    public function __construct(ITripDayService $tripDayService)
    {
        $this->tripDayService = $tripDayService;
    }

    /*
    |--------------------------------------------------------------------------
    | Update Trip Day
    |--------------------------------------------------------------------------
    */

    public function update(
        int $id,
        Request $request
    ): JsonResponse {

        $tripDay = $this->tripDayService->updateTripDay(
            $id,
            auth()->id(),
            $request->all()
        );

        if (!$tripDay) {
            return response()->json([
                'message' => 'Trip day not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'message' => 'Trip day updated successfully.',
            'data' => $tripDay,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Add Attraction
    |--------------------------------------------------------------------------
    */

    public function addAttraction(
        int $id,
        Request $request
    ): JsonResponse {

        $request->validate([
            'attraction_id' => [
                'required',
                'integer',
                'exists:attractions,id',
            ],
        ]);

        $tripDay = $this->tripDayService->addAttraction(
            $id,
            auth()->id(),
            $request->integer('attraction_id')
        );

        if (!$tripDay) {
            return response()->json([
                'message' => 'Trip day not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'message' => 'Attraction added successfully.',
            'data' => $tripDay,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Remove Attraction
    |--------------------------------------------------------------------------
    */

    public function removeAttraction(
        int $id,
        int $attractionId
    ): JsonResponse {

        $tripDay = $this->tripDayService->removeAttraction(
            $id,
            auth()->id(),
            $attractionId
        );

        if (!$tripDay) {
            return response()->json([
                'message' => 'Trip day not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'message' => 'Attraction removed successfully.',
            'data' => $tripDay,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Replace Attraction
    |--------------------------------------------------------------------------
    */

    public function replaceAttraction(
        int $id,
        Request $request
    ): JsonResponse {

        $request->validate([
            'old_attraction_id' => [
                'required',
                'integer',
                'exists:attractions,id',
            ],

            'new_attraction_id' => [
                'required',
                'integer',
                'exists:attractions,id',
            ],
        ]);

        $tripDay = $this->tripDayService->replaceAttraction(
            $id,
            auth()->id(),
            $request->integer('old_attraction_id'),
            $request->integer('new_attraction_id')
        );

        if (!$tripDay) {
            return response()->json([
                'message' => 'Trip day not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'message' => 'Attraction replaced successfully.',
            'data' => $tripDay,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Reorder Attractions
    |--------------------------------------------------------------------------
    */

    public function reorderAttractions(
        int $id,
        Request $request
    ): JsonResponse {

        $request->validate([
            'attractions' => [
                'required',
                'array',
                'min:1',
            ],

            'attractions.*.id' => [
                'required',
                'integer',
                'exists:attractions,id',
            ],

            'attractions.*.order' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        $tripDay = $this->tripDayService->reorderAttractions(
            $id,
            auth()->id(),
            $request->input('attractions')
        );

        if (!$tripDay) {
            return response()->json([
                'message' => 'Trip day not found or unauthorized.',
            ], 404);
        }

        return response()->json([
            'message' => 'Attractions reordered successfully.',
            'data' => $tripDay,
        ]);
    }
}
