<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
    ];

    public function attractions(): BelongsToMany
    {
        return $this->belongsToMany(Attraction::class);
    }
}