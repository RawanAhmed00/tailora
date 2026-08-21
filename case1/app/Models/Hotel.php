<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'city',
        'neighborhood',
        'distance_km',
        'price_per_night',
        'rating',
        'review_count',
        'amenities',
        'available_rooms',
    ];

    public function bookings(): HasMany
{
    return $this->hasMany(Booking::class);
}
}
