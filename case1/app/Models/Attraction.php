<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attraction extends Model
{
      use HasFactory;
    protected $fillable = [
        'city_id',
        'name',
        'description',
        'latitude',
        'longitude',
        'image',
        'price',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
public function tripDays(): BelongsToMany
{
    return $this->belongsToMany(
        TripDay::class,
        'trip_day_attraction'
    )
    ->withPivot('order');
}


}