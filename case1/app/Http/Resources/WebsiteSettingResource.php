<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
       'id' => $this->id,
       'site_name' => $this->site_name,
       'email' => $this->email,
       'phone' => $this->phone,
       'address' => $this->address,

    'logo' => $this->logo
        ? asset('storage/'.$this->logo)
        : null,

    'homepage_banner' => $this->homepage_banner
        ? asset('storage/'.$this->homepage_banner)
        : null,

    'social_media_links' => $this->socialMediaLinks,
];
    }
}