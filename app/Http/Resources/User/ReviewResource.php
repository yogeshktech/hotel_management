<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'service_rating' => $this->service_rating,
            'food_rating' => $this->food_rating,
            'overall_rating' => $this->overall_rating,
            'comment' => $this->comment,
            'property' => new PropertyResource($this->whenLoaded('homestay')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
