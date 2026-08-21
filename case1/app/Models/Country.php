<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'official_name',
        'code2',
        'code3',
        'flag',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
    public function trips(): HasMany
{
    return $this->hasMany(Trip::class);
}
}