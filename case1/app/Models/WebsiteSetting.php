<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'logo',
        'site_name',
        'email',
        'phone',
        'address',
        'homepage_banner',
    ];

    public function socialMediaLinks(): HasMany
    {
        return $this->hasMany(SocialMediaLink::class);
    }
}