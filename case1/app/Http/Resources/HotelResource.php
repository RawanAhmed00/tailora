<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HotelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
         return [
        'id' => $this->id,
        'name' => $this->name,
        'city' => $this->city,
        'neighborhood' => $this->neighborhood,
        'distance_km' => $this->distance_km,
        'price_per_night' => $this->price_per_night,
        'rating' => $this->rating,
        'review_count' => $this->review_count,
        'amenities' => $this->amenities,
        'available_rooms' => $this->available_rooms,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
        return parent::toArray($request);
    }
}
