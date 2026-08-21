<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\ICityService;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected ICityService $cityService;

    public function __construct(ICityService $cityService)
    {
        $this->cityService = $cityService;
    }

    public function index()
    {
        return response()->json($this->cityService->getAll());
    }

    public function show(int $id)
    {
        return response()->json($this->cityService->getById($id));
    }

    public function store(Request $request)
    {
        $request->validate([
           'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        return response()->json(
            $this->cityService->create($request),
            201
        );
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        return response()->json(
            $this->cityService->update($id, $request)
        );
    }

    public function destroy(int $id)
    {
        $this->cityService->delete($id);

        return response()->json([
            'message' => 'City deleted successfully'
        ]);
    }
}