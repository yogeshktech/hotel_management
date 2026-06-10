<?php

namespace App\Http\Resources\User;

use App\Models\RoomPricing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomPricingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'package_type' => $this->package_type,
            'label' => RoomPricing::packageLabel($this->package_type, $this->child_count),
            'adult_count' => $this->adult_count,
            'child_count' => $this->child_count,
            'price_per_night' => $this->price_per_night,
        ];
    }
}
