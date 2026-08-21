<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TripDay extends Model
{
    protected $fillable = [
        'trip_id',
        'city_id',
        'day_number',
        'date',
        'estimated_expenses',
        'transportation_tips',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'estimated_expenses' => 'decimal:2',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

  public function attractions(): BelongsToMany
    {
        return $this->belongsToMany(
            Attraction::class,
            'trip_day_attraction'
        )->withPivot('order');
    }

}