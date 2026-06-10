<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_reference' => $this->booking_reference,
            'booking_channel' => $this->booking_channel,
            'guest_package' => $this->guest_package,
            'adults_count' => $this->adults_count,
            'children_count' => $this->children_count,
            'guests' => $this->guests,
            'check_in' => $this->check_in?->format('Y-m-d'),
            'check_out' => $this->check_out?->format('Y-m-d'),
            'booked_at' => $this->booked_at?->toIso8601String(),
            'checked_in_at' => $this->checked_in_at?->toIso8601String(),
            'checked_out_at' => $this->checked_out_at?->toIso8601String(),
            'vacant_from' => $this->vacant_from?->format('Y-m-d'),
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'payment_method' => $this->payment_method,
            'base_price' => $this->base_price,
            'cleaning_fee' => $this->cleaning_fee,
            'service_fee' => $this->service_fee,
            'promo_discount' => $this->promo_discount,
            'total_price' => $this->total_price,
            'currency' => $this->currency,
            'guest_notes' => $this->guest_notes,
            'nights' => $this->nights,
            'is_occupied' => $this->is_occupied,
            'property' => new PropertyResource($this->whenLoaded('homestay')),
            'room' => new RoomResource($this->whenLoaded('room')),
            'review' => new ReviewResource($this->whenLoaded('review')),
        ];
    }
}
