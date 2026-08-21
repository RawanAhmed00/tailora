<?php

namespace App\Http\Controllers;
use App\Repo\Interfaces\IWeatherRepo;
use App\Http\Resources\WeatherResource;
use App\Services\Interfaces\IWeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(IWeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $tripId)
    {
        $request->validate(['city' => 'required|string',
        ]);
         $weather = $this->weatherService->getWeatherFromApi(
            $request->city,
            $tripId
        );
        if (!$weather) {
            return response()->json([
                'success' => false,
                'message' => 'Weather data could not be retrieved'
            ], 404);
        }
        return new WeatherResource($weather);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, int $tripId)
    {
        $weather = $this->weatherService
            ->getWeatherByTripId($tripId);

        return WeatherResource::collection($weather);
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    }
}
