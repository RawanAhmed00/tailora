<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMediaLink extends Model
{
    protected $fillable = [
        'website_setting_id',
        'type',
        'link',
    ];

    public function websiteSetting(): BelongsTo
    {
        return $this->belongsTo(WebsiteSetting::class);
    }
}