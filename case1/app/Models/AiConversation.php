<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiConversation extends Model
{
    protected $fillable = [
        'user_id',
        'trip_id',
        'type',
        'status',
    ];
protected $casts = [
    'data' => 'array',
];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(
            AiConversationMessage::class
        );
    }
    public function plans(): HasMany
{
    return $this->hasMany(
        AiTravelPlan::class,
        'ai_conversation_id'
    );
}
}