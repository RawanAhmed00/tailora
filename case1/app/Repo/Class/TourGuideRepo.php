<?php

namespace App\Repo\Class;


use App\Models\Trip;
use App\Models\Payment;
use Carbon\Carbon;
use App\Models\ReviewsManagement;
use App\Repo\Interfaces\TourGuideRepositoryInterface;

class TourGuideRepo implements TourGuideRepositoryInterface

{
    public function getSchedule()
    { 
        
        return Trip::with(['user', 'country'])->orderBy('start_date')->get();
    }

    // Implementing methods required by TourGuideRepositoryInterface
    public function getEarnings()
    {
        $today =Payment::whereDate('paid_at', Carbon::today())->where('status', 'paid')->sum('amount');
       
        $month =Payment::whereMonth('paid_at', Carbon::now()->month)->whereYear('paid_at', Carbon::now()->year)->where('status', 'paid')->sum('amount');

        $total = Payment::where ('status', 'paid')->sum('amount');

        return [
            'today_earnings' => $today,
            'month_earnings' => $month,
            'total_earnings' => $total,
        ];
    }

    public function getEarningsHistory()
    {
        // Return earnings grouped by trip start_date (example implementation)
        return Payment::with(['booking.user'])
            ->where('status', 'paid')
            ->orderBy('paid_at', 'desc')
            ->get();
    }

    public function getReviews()
{
    return ReviewsManagement::with(['user', 'trip'])->latest()->get();
}

public function getRating()
{
    $average = ReviewsManagement::avg('rating');
    $total = ReviewsManagement::count();

    return [
        'average_rating' => round($average, 1),
        'total_reviews' => $total,
    ];
}


}