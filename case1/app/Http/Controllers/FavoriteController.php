<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavoriteResource;
use App\Http\Requests\FavoriteRequest;
use App\Services\Interfaces\FavoriteServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class FavoriteController extends Controller
{
    protected FavoriteServiceInterface $favoriteService;

    public function __construct(FavoriteServiceInterface $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function index(Request $request)
    {
       return FavoriteResource::collection(
    $this->favoriteService->getUserFavorites($request->user()->id)
);
    }

    public function store(FavoriteRequest $request)
    {
        try {

            $favorite = $this->favoriteService->addFavorite(
                $request->user()->id,
                $request->type,
                $request->id
            );

            return response()->json([
                'message' => 'Added to favorites successfully.',
               'data' => new FavoriteResource($favorite)
            ], 201);

        } catch (InvalidArgumentException $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);

        }
    }

    public function destroy(FavoriteRequest $request): JsonResponse
    {
        try {

            $deleted = $this->favoriteService->removeFavorite(
                $request->user()->id,
                $request->type,
                $request->id
            );

            if (!$deleted) {
                return response()->json([
                    'message' => 'Favorite not found.'
                ], 404);
            }

            return response()->json([
                'message' => 'Removed from favorites successfully.'
            ]);

        } catch (InvalidArgumentException $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 400);

        }
    }
}