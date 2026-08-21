<?php

namespace App\Http\Controllers;
use App\Models\Trip;
use App\Models\User;
use App\Http\Requests\UpdateTripRequest;
use App\Http\Resources\TripResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;



class TripControllerAdmin extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perpage = request()->input('per_page', 10);
        $page = request()->input('page', 1);
        $cachekey = "trips_admin_all_{$page}_{$perpage}";
        $trips = Cache::remember($cachekey, now()->addHour(), function () use ($perpage) {
            return Trip::with(['user:id,name,email', 'country:id,name'])->latest()->paginate($perpage);
        });
        return response()->json([
            'success' => true,
            'message' => 'All trips retrieved successfully',
            'data' => TripResource::collection($trips),
            'meta' => [
                'current_page' => $trips->currentPage(),
                'last_page' => $trips->lastPage(),
                'per_page' => $trips->perPage(),
                'total' => $trips->total(),
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function update(UpdateTripRequest $request, Trip $trip)
    {
        $trip->update($request->validated());
        $perpage = request()->input('per_page', 10);
        Cache::forget("trips_{$trip->user_id}_{$trip->id}");
        Cache::forget("trips_user_{$trip->user_id}_1_{$perpage}");
        Cache::forget("trips_admin_all_1_{$perpage}");
        Cache::forget("admin_trips_statistics");

        return response()->json([
            'success' => true,
            'message' => 'Trip updated successfully by Admin',
            'data' => new TripResource($trip->load(['user', 'country']))
        ], 200);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Trip $trip)
    {
        $owner = $trip->user_id;
        $tripId = $trip->id;
        $trip->delete();
        $perpage = request()->input('per_page', 10);
        Cache::forget("trips_{$owner}_{$tripId}");
        Cache::forget("trips_user_{$owner}_1_{$perpage}");
        Cache::forget("trips_admin_all_1_{$perpage}");
        Cache::forget("admin_trips_statistics");
        return response()->json([
            'success' => true,
            'message' => 'Trip deleted successfully by Admin'
        ], 200);
    }

    

    public function TripStatistics()
    {
        $cachekey = "admin_trips_statistics";
        $stats = Cache::remember($cachekey, now()->addHour(), function () {
            return [
                'total_trips' => Trip::count(),
                'trips_created_today' => Trip::whereDate('created_at', now()->today())->count(),
                'trips_this_month' => Trip::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
                'latest_trips' => Trip::with(['user:id,name,email', 'country:id,name'])->latest()->take(5)->get(),
                'top_users' => User::withCount('trips')->orderByDesc('trips_count')->take(5)->get(['id', 'name', 'email']),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Trip statistics retrieved successfully',
            'data' => $stats
        ], 200);
    }
}
