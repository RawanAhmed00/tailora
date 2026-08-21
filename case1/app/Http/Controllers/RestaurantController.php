<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Services\Class\RestaurantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    protected RestaurantService $restaurantService;

    public function __construct(RestaurantService $restaurantService)
    {
        $this->restaurantService = $restaurantService;
    }

    public function index(Request $request)
    {
        $filters = $request->only([
            'search',
            'city_id',
            'category_id',
            'min_rating',
            'sort',
            'per_page',
        ]);

        return RestaurantResource::collection(
            $this->restaurantService->getAll($filters)
        );
    }

    public function show(int $id)
    {
        $restaurant = $this->restaurantService->getById($id);

        if (!$restaurant) {
            return response()->json([
                'message' => 'Restaurant not found'
            ], 404);
        }

        return new RestaurantResource($restaurant);
    }

    public function store(StoreRestaurantRequest $request)
    {
        $restaurant = $this->restaurantService->create(
            $request->validated()
        );

        return new RestaurantResource($restaurant);
    }

    public function update(UpdateRestaurantRequest $request, int $id)
    {
        $updated = $this->restaurantService->update(
            $id,
            $request->validated()
        );

        if (!$updated) {
            return response()->json([
                'message' => 'Restaurant not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Restaurant updated successfully'
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->restaurantService->delete($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Restaurant not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Restaurant deleted successfully'
        ]);
    }
}