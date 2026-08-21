<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiTravelPlan extends Model
{
    protected $fillable = [
        'user_id',
        'ai_conversation_id',
        'destination',
        'number_of_days',
        'budget',
        'number_of_travelers',
        'travel_style',
        'interests',
        'plan_name',
        'description',
        'cities',
        'itinerary',
        'is_selected',
    ];

    protected $casts = [
        'budget' => 'decimal:2',

        'interests' => 'array',

        'cities' => 'array',

        'itinerary' => 'array',

        'is_selected' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(
            AiConversation::class,
            'ai_conversation_id'
        );
    }
}