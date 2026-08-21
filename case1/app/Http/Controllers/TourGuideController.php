<?php 

namespace App\Http\Controllers;

use App\Repo\Interfaces\TourGuideRepositoryInterface;

class TourGuideController extends Controller
{
    protected TourGuideRepositoryInterface $tourGuideRepository;

    public function __construct(TourGuideRepositoryInterface $tourGuideRepository)
    {
        $this->tourGuideRepository = $tourGuideRepository;
    }

    public function schedule()
    {
        return response()->json([
            "success"=>true,
            "data"=>$this->tourGuideRepository->getSchedule()
        ]);
    }

    public function earnings()
    {
        return response()->json([
            "success"=>true,
            "data"=>$this->tourGuideRepository->getEarnings()
        ]);
    }

    public function earningsHistory()
    {
        return response()->json([
            "success"=>true,
            "data"=>$this->tourGuideRepository->getEarningsHistory()
        ]);
    }

    public function reviews()
{
    return response()->json([
        "success" => true,
        "data" => $this->tourGuideRepository->getReviews()
    ]);
}

public function rating()
{
    return response()->json([
        "success" => true,
        "data" => $this->tourGuideRepository->getRating()
    ]);
}
}