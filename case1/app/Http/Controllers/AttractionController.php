<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreAttractionRequest;
use App\Http\Requests\UpdateAttractionRequest;
use App\Services\Interfaces\IAttractionService;
use Illuminate\Http\Request;

class AttractionController extends Controller
{
    public function __construct(
        protected IAttractionService $attractionService
    ) {}

  public function index(Request $request)
{
   $perPage = request()->input('per_page', 10);

    return response()->json(
        $this->attractionService->getAll($perPage)
    );
}

    public function show(int $id): JsonResponse
    {
        $attraction = $this->attractionService->getById($id);

        if (!$attraction) {
            return response()->json([
                'success' => false,
                'message' => 'Attraction not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $attraction,
        ]);
    }

    public function store(StoreAttractionRequest $request): JsonResponse
    {
        $attraction = $this->attractionService->create($request);

        return response()->json([
            'success' => true,
            'message' => 'Attraction created successfully.',
            'data' => $attraction,
        ], 201);
    }

    public function update(UpdateAttractionRequest $request, int $id): JsonResponse
    {
        $attraction = $this->attractionService->update($request, $id);

        if (!$attraction) {
            return response()->json([
                'success' => false,
                'message' => 'Attraction not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attraction updated successfully.',
            'data' => $attraction,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->attractionService->delete($id);

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Attraction not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attraction deleted successfully.',
        ]);
    }
}