<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weather extends Model
{
    protected $table = 'weathers';

    protected $fillable = [
        'trip_id',
        'city',
        'country',
        'latitude',
        'longitude',
        'temperature',
        'humidity',
        'wind_speed',
        'condition',
        'forecast_date',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}