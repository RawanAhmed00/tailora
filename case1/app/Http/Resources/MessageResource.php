<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $authId = auth()->id();


        $partner = $this->sender_id === $authId ? $this->receiver : $this->sender;

        return [
            'id' => $this->id,
            'sender_id' => $this->sender_id,
            'receiver_id' => $this->receiver_id,
            'message' => $this->message,
            'is_read' => (bool) $this->is_read,
            
            
            'sender' => [
                'id' => $this->sender?->id,
                'name' => $this->sender?->name,
            ],

            
            'partner' => [
                'id' => $partner?->id,
                'name' => $partner?->name,
            ],

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}