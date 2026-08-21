<?php

namespace App\Repo\Class;

use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\ReviewsManagement;
use App\Models\TourGuideRequest;
use App\Models\Trip;
use App\Models\User;
use App\Repo\Interfaces\IAnalyticsRepository;
use Illuminate\Support\Facades\DB;

class AnalyticsRepository implements IAnalyticsRepository
{
    public function dashboard()
    {
        $user = auth()->user();
        if ($user && $user->role === 'admin') {
            return $this->getAdminDashboard();
        }

        $userId = auth()->id();

        return [
            'user' => User::find($userId),
            'statistics' => $this->getStatistics($userId),
            'saved_trips' => $this->getSavedTrips($userId),
            'favorite_destinations' => $this->getFavorites($userId),
            'booking_history' => $this->getBookingHistory($userId),
        ];
    }

    public function getAdminDashboard(): array
    {
        $revenue = (float) Booking::whereIn('status', ['confirmed', 'completed', 'paid'])->sum('total_price');
        if ($revenue <= 0) {
            $revenue = (float) Booking::sum('total_price');
        }

        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        $totalUsers = User::count();
        $tourGuides = User::where('role', 't_guide')->count();
        $totalTrips = Trip::count();
        $totalHotels = Hotel::count();
        $totalRestaurants = Restaurant::count();

        $popular = Trip::join('countries', 'trips.country_id', '=', 'countries.id')
            ->select('countries.name as country', DB::raw('count(*) as total_trips'))
            ->groupBy('countries.name', 'trips.country_id')
            ->orderByDesc('total_trips')
            ->take(6)
            ->get();

        $currentYear = now()->year;
        $monthlyTrips = Trip::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $userGrowth = User::select(DB::raw('MONTH(created_at) as month'), DB::raw('count(*) as total'))
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        $monthlyRevenue = Booking::select(DB::raw('MONTH(created_at) as month'), DB::raw('COALESCE(sum(total_price), 0) as revenue'))
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get();

        return [
            'total_revenue' => $revenue,
            'total_bookings' => $totalBookings,
            'confirmed_bookings' => $confirmedBookings,
            'cancelled_bookings' => $cancelledBookings,
            'pending_bookings' => $pendingBookings,
            'total_users' => $totalUsers,
            'total_tour_guides' => $tourGuides,
            'total_trips' => $totalTrips,
            'total_hotels' => $totalHotels,
            'total_restaurants' => $totalRestaurants,
            'most_popular_destinations' => $popular,
            'monthly_trips' => $monthlyTrips,
            'user_growth' => $userGrowth,
            'monthly_revenue' => $monthlyRevenue,
            'statistics' => [
                'total_trips' => $totalTrips,
                'completed_trips' => Trip::where('status', 'completed')->count(),
                'upcoming_trips' => Trip::where('status', 'upcoming')->count(),
                'cancelled_trips' => Trip::where('status', 'cancelled')->count(),
                'total_bookings' => $totalBookings,
                'total_users' => $totalUsers,
            ]
        ];
    }

    public function getStatistics(int $userId): array
    {
        return [
            'total_trips' => Trip::where('user_id', $userId)->count(),

            'completed_trips' => Trip::where('user_id', $userId)
                ->where('status', 'completed')
                ->count(),

            'upcoming_trips' => Trip::where('user_id', $userId)
                ->where('status', 'upcoming')
                ->count(),

            'cancelled_trips' => Trip::where('user_id', $userId)
                ->where('status', 'cancelled')
                ->count(),

            'total_bookings' => Booking::where('user_id', $userId)->count(),

            'total_favorites' => Favorite::where('user_id', $userId)->count(),
        ];
    }

    public function getSavedTrips(int $userId)
    {
        return Trip::where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }

    public function getFavorites(int $userId)
    {
        return Favorite::with('favoritable')
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }

    public function getBookingHistory(int $userId)
    {
        return Booking::where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }
public function getTourGuideDashboard(int $tourGuideId): array
{
    $todayBookings = TourGuideRequest::where('tour_guide_id', $tourGuideId)
        ->whereDate('created_at', today())
        ->count();

    $upcomingBookings = TourGuideRequest::where('tour_guide_id', $tourGuideId)
        ->where('status', 'accepted')
        ->whereDate('created_at', '>', today())
        ->count();

    $completedTrips = TourGuideRequest::where('tour_guide_id', $tourGuideId)
        ->where('status', 'completed')
        ->count();

    $monthlyEarnings = TourGuideRequest::where('tour_guide_id', $tourGuideId)
        ->where('status', 'completed')
        ->whereMonth('updated_at', now()->month)
        ->whereYear('updated_at', now()->year)
        ->sum('price');

    $totalEarnings = TourGuideRequest::where('tour_guide_id', $tourGuideId)
        ->where('status', 'completed')
        ->sum('price');

    return [
        'today_bookings' => $todayBookings,
        'upcoming_bookings' => $upcomingBookings,
        'completed_trips' => $completedTrips,
        'monthly_earnings' => (float) $monthlyEarnings,
        'total_earnings' => (float) $totalEarnings,
    ];
}

}