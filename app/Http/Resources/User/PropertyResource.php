<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'max_guests' => $this->max_guests,
            'bedrooms' => $this->bedrooms,
            'beds' => $this->beds,
            'bathrooms' => $this->bathrooms,
            'price_per_night' => $this->price_per_night,
            'cleaning_fee' => $this->cleaning_fee,
            'currency' => $this->currency,
            'amenities' => $this->amenities,
            'house_rules' => $this->house_rules,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'location' => new LocationResource($this->whenLoaded('location')),
            'images' => PropertyImageResource::collection($this->whenLoaded('images')),
            'rooms' => RoomResource::collection($this->whenLoaded('rooms')),
            'average_rating' => $this->when(isset($this->reviews_avg_overall_rating), round($this->reviews_avg_overall_rating, 1)),
        ];
    }
}
