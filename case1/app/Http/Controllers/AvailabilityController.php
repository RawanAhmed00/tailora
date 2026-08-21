<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Interfaces\IAvailabilityService;
use App\Http\Resources\AvailabilityResource;
class AvailabilityController extends Controller
{
    protected $availabilityService;

    public function __construct(IAvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }
    public function index()
    {
        $availabilities = $this->availabilityService->getAllAvailabilities();
        return AvailabilityResource::collection($availabilities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_available' => 'boolean',
        ]);
          $data['tour_guide_id'] = $request->user()->id;

        $availability = $this->availabilityService->createAvailable($data);
        return response()->json(new AvailabilityResource($availability), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data=$request->validate([
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'is_available' => 'sometimes|boolean',
        ]);
        $availability = $this->availabilityService->updateAvailable($id, $data);
        return response()->json(new AvailabilityResource($availability));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->availabilityService->deleteAvailable($id);

        return response()->json([
            'success' => true,
            'message' => 'Availability deleted successfully',
        ]);
    }
}
