<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHotelRequest;
use App\Http\Requests\UpdateHotelRequest;
use App\Http\Resources\HotelResource;
use App\Services\Class\HotelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    protected HotelService $hotelService;

    public function __construct(HotelService $hotelService)
    {
        $this->hotelService = $hotelService;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'city_id',
            'min_rating',
            'max_price',
            'sort',
            'per_page',
        ]);

        return HotelResource::collection(
            $this->hotelService->getAll($filters)
        );
    }

    public function show(int $id)
    {
        $hotel = $this->hotelService->getById($id);

        if (!$hotel) {
            return response()->json([
                'message' => 'Hotel not found'
            ], 404);
        }

        return new HotelResource($hotel);
    }

    public function store(StoreHotelRequest $request)
    {
        $hotel = $this->hotelService->create(
            $request->validated()
        );

        return new HotelResource($hotel);
    }

    public function update(UpdateHotelRequest $request, int $id)
    {
        $updated = $this->hotelService->update(
            $id,
            $request->validated()
        );

        if (!$updated) {
            return response()->json([
                'message' => 'Hotel not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Hotel updated successfully'
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->hotelService->delete($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Hotel not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Hotel deleted successfully'
        ]);
    }
}