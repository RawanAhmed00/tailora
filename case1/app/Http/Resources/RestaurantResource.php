<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
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
    'address' => $this->address,
    'locality' => $this->locality,
    'latitude' => $this->latitude,
    'longitude' => $this->longitude,
    'cuisines' => $this->cuisines,
    'average_cost_for_two' => $this->average_cost_for_two,
    'currency' => $this->currency,
    'has_table_booking' => $this->has_table_booking,
    'has_online_delivery' => $this->has_online_delivery,
    'is_delivering_now' => $this->is_delivering_now,
    'price_range' => $this->price_range,
    'rating' => $this->rating,
    'votes' => $this->votes,
    'created_at' => $this->created_at,
    'updated_at' => $this->updated_at,
];
    }
}
