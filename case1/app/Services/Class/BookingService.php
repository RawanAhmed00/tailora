<?php

namespace App\Services\Class;

use App\Models\Availability;
use App\Models\Booking;
use App\Models\Flight;
use App\Models\Hotel;
use App\Models\TourGuideRequest;
use App\Models\User;
use App\Repo\Interfaces\IBookingRepository;
use App\Services\Interfaces\IBookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService implements IBookingService
{
    public function __construct(
        protected IBookingRepository $bookingRepository
    ) {
    }

    public function getUserBookings(int $userId)
    {
        return $this->bookingRepository->getUserBookings($userId);
    }

    public function getUserBookingById(int $userId, int $id)
    {
        return $this->bookingRepository->getUserBookingById(
            $userId,
            $id
        );
    }

    public function createBooking(int $userId, array $data)
    {
        $hotelPrice = 0;
        if (!empty($data['hotel_id'])) {
            $hotel = Hotel::findOrFail($data['hotel_id']);
            $nights = !empty($data['number_of_nights']) ? (int) $data['number_of_nights'] : 1;
            $hotelPrice = $hotel->price_per_night * $nights;
        }

        $flightPrice = 0;
        if (!empty($data['flight_id'])) {
            $flight = Flight::findOrFail($data['flight_id']);
            $flightPrice = $flight->price;
        }

        $tourGuidePrice = 0;
        $wantsTourGuide = !empty($data['wants_tour_guide']) && filter_var($data['wants_tour_guide'], FILTER_VALIDATE_BOOLEAN);

        if ($wantsTourGuide) {
            $tourGuidePrice = 100;
        }

        /*
        |--------------------------------------------------------------------------
        | Create Booking (Guide is not assigned yet - Admin assigns available guide)
        |--------------------------------------------------------------------------
        */
        $data['user_id'] = $userId;
        $data['total_price'] = $hotelPrice + $flightPrice + $tourGuidePrice;
        $data['wants_tour_guide'] = $wantsTourGuide;
        $data['tour_guide_id'] = null;
        $data['status'] = 'pending';

        $booking = $this->bookingRepository->createBooking($data);

        return $booking->fresh([
            'flight',
            'hotel',
            'tourGuide',
            'trip.country',
            'tourGuideRequests.tourGuide',
        ]);
    }

    public function assignTourGuide(int $bookingId, int $tourGuideId)
    {
        $booking = Booking::with('trip')->findOrFail($bookingId);

        $guide = User::where('id', $tourGuideId)
            ->where('role', 't_guide')
            ->firstOrFail();

        // Mark any existing pending requests for this booking as rejected
        TourGuideRequest::where('booking_id', $bookingId)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'rejected_at' => now(),
            ]);

        // Create new pending TourGuideRequest for the selected guide
        TourGuideRequest::create([
            'booking_id' => $booking->id,
            'trip_id' => $booking->trip_id,
            'tour_guide_id' => $guide->id,
            'price' => 100.00,
            'status' => 'pending',
        ]);

        // Ensure tour_guide_id remains unconfirmed until the guide explicitly accepts
        $booking->update([
            'tour_guide_id' => null,
            'wants_tour_guide' => true,
        ]);

        return $booking->fresh([
            'user',
            'flight',
            'hotel',
            'tourGuide',
            'trip.country',
            'tourGuideRequests.tourGuide',
        ]);
    }

    public function getAvailableTourGuides(
        ?int $bookingId = null,
        ?string $requestDate = null,
        ?string $requestTime = null
    ) {
        $guides = User::where('role', 't_guide')->with('availabilities')->get();

        $booking = null;
        $startDate = null;
        $endDate = null;

        if ($bookingId) {
            $booking = Booking::with('trip')->find($bookingId);
            if ($booking && $booking->trip) {
                $startDate = $booking->trip->start_date ? Carbon::parse($booking->trip->start_date)->toDateString() : null;
                $endDate = $booking->trip->end_date ? Carbon::parse($booking->trip->end_date)->toDateString() : null;
            }
        }

        // Target date to evaluate
        $targetDate = $requestDate ?: ($startDate ?: now()->toDateString());
        $targetDateCarbon = Carbon::parse($targetDate)->startOfDay();
        $isToday = $targetDateCarbon->isToday();
        $isPast = $targetDateCarbon->isPast() && !$isToday;
        $isFuture = $targetDateCarbon->isFuture() && !$isToday;

        // Parse requested time if provided (e.g. "12:00", "15:00:00")
        $hasRequestedTime = !empty($requestTime);
        $requestedTimeCarbon = null;
        if ($hasRequestedTime) {
            try {
                $parsedTime = Carbon::parse($requestTime);
                $requestedTimeStr = $parsedTime->format('H:i:s');
                $requestedTimeCarbon = Carbon::parse($targetDate . ' ' . $requestedTimeStr);
            } catch (\Exception $e) {
                $hasRequestedTime = false;
            }
        }

        return $guides->map(function ($guide) use ($startDate, $endDate, $targetDate, $targetDateCarbon, $isToday, $isPast, $isFuture, $hasRequestedTime, $requestedTimeCarbon, $requestTime) {
            $isAvailable = true;
            $conflictReason = null;
            $activeSlot = null;

            // 1. Account status check
            if ($guide->is_active === 0 || $guide->is_active === false) {
                return [
                    'id' => $guide->id,
                    'name' => $guide->name,
                    'email' => $guide->email,
                    'phone_num' => $guide->phone_num,
                    'dist_country' => $guide->dist_country,
                    'gender' => $guide->gender,
                    'is_available' => false,
                    'conflict_reason' => 'Account inactive',
                    'available_slots' => [],
                ];
            }

            // 2. Overlapping accepted booking conflicts
            if ($startDate && $endDate) {
                $hasConflict = TourGuideRequest::where('tour_guide_id', $guide->id)
                    ->where('status', 'accepted')
                    ->whereHas('trip', function ($q) use ($startDate, $endDate) {
                        $q->where(function ($sub) use ($startDate, $endDate) {
                            $sub->whereBetween('start_date', [$startDate, $endDate])
                                ->orWhereBetween('end_date', [$startDate, $endDate])
                                ->orWhere(function ($s) use ($startDate, $endDate) {
                                    $s->where('start_date', '<=', $startDate)
                                      ->where('end_date', '>=', $endDate);
                                });
                        });
                    })
                    ->exists();

                if ($hasConflict) {
                    return [
                        'id' => $guide->id,
                        'name' => $guide->name,
                        'email' => $guide->email,
                        'phone_num' => $guide->phone_num,
                        'dist_country' => $guide->dist_country,
                        'gender' => $guide->gender,
                        'is_available' => false,
                        'conflict_reason' => 'Has conflicting accepted tour',
                        'available_slots' => [],
                    ];
                }
            }

            // 3. Time-based availability evaluation against availabilities table
            $allSlots = $guide->availabilities ?? collect();
            if ($allSlots->isEmpty()) {
                $allSlots = Availability::where('tour_guide_id', $guide->id)->get();
            }

            // If guide has never set any availability slots
            if ($allSlots->isEmpty()) {
                return [
                    'id' => $guide->id,
                    'name' => $guide->name,
                    'email' => $guide->email,
                    'phone_num' => $guide->phone_num,
                    'dist_country' => $guide->dist_country,
                    'gender' => $guide->gender,
                    'is_available' => false,
                    'conflict_reason' => 'No availability slots defined',
                    'available_slots' => [],
                ];
            }

            // Filter slots on the target date
            $targetSlots = $allSlots->filter(function ($s) use ($targetDate) {
                $slotDate = Carbon::parse($s->date)->toDateString();
                return $slotDate === $targetDate && (bool) $s->is_available;
            })->values();

            // Find future slots for fallback / info display
            $futureSlots = $allSlots->filter(function ($s) {
                if (!(bool) $s->is_available) return false;
                $sDate = Carbon::parse($s->date)->toDateString();
                $sEndTime = $s->end_time;
                $sEndDateTime = Carbon::parse($sDate . ' ' . $sEndTime);
                return $sEndDateTime->isFuture();
            })->sortBy(function ($s) {
                return Carbon::parse(Carbon::parse($s->date)->toDateString() . ' ' . $s->start_time)->timestamp;
            })->values();

            // Evaluate according to target date and time conditions
            if ($isPast) {
                // The requested date has completely passed
                $isAvailable = false;
                $conflictReason = 'Date is in the past';
            } elseif ($hasRequestedTime) {
                // Specific time requested (e.g. 12:00, 15:00)
                if ($targetSlots->isEmpty()) {
                    $isAvailable = false;
                    $nextSlot = $futureSlots->first();
                    $conflictReason = $nextSlot 
                        ? 'No availability on ' . $targetDate . ' (Next: ' . Carbon::parse($nextSlot->date)->toDateString() . ' ' . substr($nextSlot->start_time, 0, 5) . '-' . substr($nextSlot->end_time, 0, 5) . ')'
                        : 'No availability defined for this date';
                } else {
                    // Check if requested time falls within any slot on target date
                    $matchingSlot = $targetSlots->first(function ($s) use ($targetDate, $requestedTimeCarbon) {
                        $sStart = Carbon::parse($targetDate . ' ' . $s->start_time);
                        $sEnd = Carbon::parse($targetDate . ' ' . $s->end_time);
                        return $requestedTimeCarbon->gte($sStart) && $requestedTimeCarbon->lte($sEnd);
                    });

                    if ($matchingSlot) {
                        // Check if the slot has already expired in real time
                        $matchingEnd = Carbon::parse($targetDate . ' ' . $matchingSlot->end_time);
                        if ($isToday && $matchingEnd->isPast()) {
                            $isAvailable = false;
                            $conflictReason = 'Availability slot (' . substr($matchingSlot->start_time, 0, 5) . ' - ' . substr($matchingSlot->end_time, 0, 5) . ') has ended';
                        } else {
                            $isAvailable = true;
                            $activeSlot = $matchingSlot;
                        }
                    } else {
                        $isAvailable = false;
                        $slotHours = $targetSlots->map(function ($s) {
                            return substr($s->start_time, 0, 5) . ' - ' . substr($s->end_time, 0, 5);
                        })->join(', ');
                        $conflictReason = 'Requested time (' . substr($requestedTimeCarbon->format('H:i'), 0, 5) . ') is outside available hours (' . $slotHours . ')';
                    }
                }
            } else {
                // No specific time requested
                if ($isToday) {
                    // 1. Is the guide currently within an active slot?
                    $currentlyActive = $targetSlots->first(function ($s) use ($targetDate) {
                        $sStart = Carbon::parse($targetDate . ' ' . $s->start_time);
                        $sEnd = Carbon::parse($targetDate . ' ' . $s->end_time);
                        return now()->gte($sStart) && now()->lte($sEnd);
                    });

                    // 2. Is there an upcoming slot later today?
                    $upcomingToday = $targetSlots->first(function ($s) use ($targetDate) {
                        $sStart = Carbon::parse($targetDate . ' ' . $s->start_time);
                        $sEnd = Carbon::parse($targetDate . ' ' . $s->end_time);
                        return now()->lt($sStart) && $sEnd->isFuture();
                    });

                    if ($currentlyActive) {
                        $isAvailable = true;
                        $activeSlot = $currentlyActive;
                    } elseif ($upcomingToday) {
                        $isAvailable = true;
                        $activeSlot = $upcomingToday;
                    } else {
                        // All slots for today have ended or no slots today
                        $isAvailable = false;
                        $nextSlot = $futureSlots->first();
                        if ($targetSlots->isNotEmpty()) {
                            $lastSlot = $targetSlots->sortByDesc('end_time')->first();
                            $conflictReason = 'Availability slot (' . substr($lastSlot->start_time, 0, 5) . ' - ' . substr($lastSlot->end_time, 0, 5) . ') has ended' .
                                ($nextSlot ? ' (Next: ' . Carbon::parse($nextSlot->date)->toDateString() . ' ' . substr($nextSlot->start_time, 0, 5) . '-' . substr($nextSlot->end_time, 0, 5) . ')' : '');
                        } else {
                            $conflictReason = $nextSlot
                                ? 'No availability today (Next: ' . Carbon::parse($nextSlot->date)->toDateString() . ' ' . substr($nextSlot->start_time, 0, 5) . '-' . substr($nextSlot->end_time, 0, 5) . ')'
                                : 'No availability slots defined for today';
                        }
                    }
                } elseif ($isFuture) {
                    // Future date: guide is available if they have at least one valid slot on that date
                    if ($targetSlots->isNotEmpty()) {
                        $isAvailable = true;
                        $activeSlot = $targetSlots->first();
                    } else {
                        $isAvailable = false;
                        $nextSlot = $futureSlots->first();
                        $conflictReason = $nextSlot
                            ? 'No availability on ' . $targetDate . ' (Next: ' . Carbon::parse($nextSlot->date)->toDateString() . ' ' . substr($nextSlot->start_time, 0, 5) . '-' . substr($nextSlot->end_time, 0, 5) . ')'
                            : 'No availability defined for ' . $targetDate;
                    }
                }
            }

            $slotsFormatted = $targetSlots->map(function ($s) {
                return [
                    'id' => $s->id,
                    'date' => Carbon::parse($s->date)->toDateString(),
                    'start_time' => substr($s->start_time, 0, 5),
                    'end_time' => substr($s->end_time, 0, 5),
                ];
            })->values()->all();

            return [
                'id' => $guide->id,
                'name' => $guide->name,
                'email' => $guide->email,
                'phone_num' => $guide->phone_num,
                'dist_country' => $guide->dist_country,
                'gender' => $guide->gender,
                'is_available' => (bool) $isAvailable,
                'conflict_reason' => $isAvailable ? null : $conflictReason,
                'available_slots' => $slotsFormatted,
                'active_slot' => $activeSlot ? [
                    'id' => $activeSlot->id,
                    'date' => Carbon::parse($activeSlot->date)->toDateString(),
                    'start_time' => substr($activeSlot->start_time, 0, 5),
                    'end_time' => substr($activeSlot->end_time, 0, 5),
                ] : null,
            ];
        });
    }

    public function getAllBookings()
    {
        return $this->bookingRepository->getAllBookings();
    }

    public function getBookingById(int $id)
    {
        return $this->bookingRepository->getBookingById($id);
    }

    public function updateBooking(int $id, array $data)
    {
        return $this->bookingRepository->updateBooking(
            $id,
            $data
        );
    }

    public function deleteBooking(int $id)
    {
        return $this->bookingRepository->deleteBooking($id);
    }
}