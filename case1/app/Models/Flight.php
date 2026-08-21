<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ignav_id',
        'origin',
        'destination',
        'departure_date',
        'return_date',
        'outbound_carrier_code',
        'outbound_flight_number',
        'inbound_carrier_code',
        'inbound_flight_number',
        'airline',
        'price',
        'currency',
        'booking_url',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'price' => 'decimal:2',
    ];

public function trips(): HasMany
{
    return $this->hasMany(Trip::class);
}
public function user() { return $this->belongsTo(User::class); }
}
