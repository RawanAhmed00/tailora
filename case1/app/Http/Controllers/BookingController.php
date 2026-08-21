<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use App\Services\Interfaces\IBookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected IBookingService $bookingService
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $bookings = $this->bookingService->getUserBookings(
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Bookings retrieved successfully',
            'data' => $bookings,
        ]);
    }

   public function store(StoreBookingRequest $request)
{
    $booking = $this->bookingService->createBooking(
        auth()->id(),
        $request->validated()
    );

    return response()->json([
        'message' => 'Booking created successfully',
        'booking' => $booking,
    ], 201);
}

    public function show(Request $request, int $id)
    {
        $booking = $this->bookingService->getUserBookingById(
            $request->user()->id,
            $id
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking retrieved successfully',
            'data' => $booking,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    public function adminIndex()
    {
        $bookings = $this->bookingService->getAllBookings();

        return response()->json([
            'success' => true,
            'message' => 'All bookings retrieved successfully',
            'data' => $bookings,
        ]);
    }

    public function adminShow(int $id)
    {
        $booking = $this->bookingService->getBookingById($id);

        return response()->json([
            'success' => true,
            'message' => 'Booking retrieved successfully',
            'data' => $booking,
        ]);
    }

    public function update(
        UpdateBookingRequest $request,
        int $id
    ) {
        $booking = $this->bookingService->updateBooking(
            $id,
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking,
        ]);
    }

    public function destroy(int $id)
    {
        $this->bookingService->deleteBooking($id);

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully',
        ]);
    }

    public function assignTourGuide(Request $request, int $id)
    {
        $data = $request->validate([
            'tour_guide_id' => 'required|integer|exists:users,id',
        ]);

        $booking = $this->bookingService->assignTourGuide($id, $data['tour_guide_id']);

        return response()->json([
            'success' => true,
            'message' => 'Tour guide assigned successfully. Awaiting guide response.',
            'data' => $booking,
        ]);
    }

    public function getAvailableTourGuides(Request $request)
    {
        $bookingId = $request->query('booking_id') ? (int) $request->query('booking_id') : null;
        $date = $request->query('date');
        $time = $request->query('time');
        
        $guides = $this->bookingService->getAvailableTourGuides($bookingId, $date, $time);

        return response()->json([
            'success' => true,
            'message' => 'Available tour guides retrieved successfully',
            'data' => $guides,
        ]);
    }
}