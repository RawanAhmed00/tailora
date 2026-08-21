<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripDayResource extends JsonResource
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
            'place_to_visit' => $this->place_to_visit,
            'activities' => $this->activities,
            'transportation_tips' => $this->transportation_tips,
            'daily_expenses' => $this->daily_expenses,
            'trip_id' => $this->trip_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
