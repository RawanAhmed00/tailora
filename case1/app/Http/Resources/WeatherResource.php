<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeatherResource extends JsonResource
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
            'trip_id' => $this->trip_id,
            'city' => $this->city,
            'country' => $this->country,
            'location' => [
                'latitude' => $this->latitude,
                 'longitude' => $this->longitude,
            ],
            'weather' => [
                'temperature' => $this->temperature,
                'humidity' => $this->humidity,
                'wind_speed' => $this->wind_speed,
                'condition' => $this->condition,
            ],
             'forecast_date' => $this->forecast_date,
        ];
    }
}
