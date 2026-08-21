<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    protected $fillable = [
        'name',
        'city',
        'address',
        'locality',
        'latitude',
        'longitude',
        'cuisines',
        'average_cost_for_two',
        'currency',
        'has_table_booking',
        'has_online_delivery',
        'is_delivering_now',
        'price_range',
        'rating',
        'votes',
    ];
}