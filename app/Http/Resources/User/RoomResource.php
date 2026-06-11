<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'room_type' => $this->room_type,
            'description' => $this->description,
            'capacity' => $this->capacity,
            'bed_count' => $this->bed_count,
            'price_per_night' => $this->price_per_night,
            'total_units' => $this->total_units,
            'amenities' => $this->amenities,
            'status' => $this->status,
            'pricings' => RoomPricingResource::collection($this->whenLoaded('pricings')),
            'images' => $this->whenLoaded('images', fn () => $this->images->map(fn ($img) => [
                'id' => $img->id,
                'url' => $img->url,
                'is_primary' => $img->is_primary,
            ])),
            'primary_image' => $this->whenLoaded('images', fn () => ($this->images->firstWhere('is_primary', true) ?? $this->images->first())?->url),
        ];
    }
}
