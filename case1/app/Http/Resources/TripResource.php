<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $numDays = null;
        if ($this->start_date && $this->end_date) {
            try {
                $start = Carbon::parse($this->start_date);
                $end = Carbon::parse($this->end_date);
                $numDays = $start->diffInDays($end) + 1;
            } catch (\Throwable $_) {
                $numDays = $this->num_days;
            }
        } else {
            $numDays = $this->num_days;
        }

        $countryName = $this->country?->name ?: $this->dis_country;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'country_id' => $this->country_id,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->toDateString() : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->toDateString() : null,
            'num_days' => $numDays,
            'travel_style' => $this->travel_style,
            'dis_country' => $countryName,
            'country_name' => $countryName,
            'country' => $this->country ? [
                'id' => $this->country->id,
                'name' => $this->country->name,
            ] : null,
            'budget' => $this->budget,
            'interests' => $this->interests,
            'travelers' => $this->travelers ?: $this->number_of_travelers,
            'number_of_travelers' => $this->travelers ?: $this->number_of_travelers,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
