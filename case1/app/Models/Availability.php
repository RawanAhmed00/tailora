<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
     protected $fillable = [
        'tour_guide_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
    ];

    public function tourGuide()
    {
        return $this->belongsTo(User::class, 'tour_guide_id');
    }


}
