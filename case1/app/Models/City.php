<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'description',
        'image',
    ];

      public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function attractions(): HasMany
    {
        return $this->hasMany(Attraction::class);
    }
public function tripDays(): HasMany
{
    return $this->hasMany(TripDay::class);
}
}