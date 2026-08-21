<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'trip_id',
        'flight_id',
        'hotel_id',
        'tour_guide_id',
        'wants_tour_guide',
        'number_of_nights',
        'total_price',
        'status',
    ];

    protected $casts = [
        'total_price' => 'decimal:2',
        'wants_tour_guide' => 'boolean',
    ];

    /**
     * Customer who created the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Trip associated with the booking.
     */
    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Flight selected for the booking.
     */
    public function flight(): BelongsTo
    {
        return $this->belongsTo(Flight::class);
    }

    /**
     * Hotel selected for the booking.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Optional tour guide.
     * Tour guide is a User with guide role.
     */
    public function tourGuide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tour_guide_id');
    }
public function tourGuideRequests(): HasMany
{
    return $this->hasMany(TourGuideRequest::class);
}
public function payment(): HasOne
{
    return $this->hasOne(Payment::class);
}
} 