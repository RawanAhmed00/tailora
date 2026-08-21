<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewsManagement extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'user_id',
        'trip_id',
        'tour_guide_id',
        'review_type',
        'rating',
        'comment',
        'status',
    ];

    protected $casts = [
        'rating' => 'integer',
        'trip_id' => 'integer',
        'tour_guide_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class, 'trip_id');
    }

    public function tourGuide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tour_guide_id');
    }
}
