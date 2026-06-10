<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Room extends Model
{
    protected $fillable = [
        'homestay_id',
        'name',
        'room_type',
        'description',
        'capacity',
        'bed_count',
        'price_per_night',
        'total_units',
        'amenities',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'price_per_night' => 'decimal:2',
        ];
    }

    public function homestay(): BelongsTo
    {
        return $this->belongsTo(Homestay::class);
    }

    public function pricings()
    {
        return $this->hasMany(RoomPricing::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function getPriceForPackage(string $packageType, int $childCount = 0): ?float
    {
        $pricing = $this->pricings()
            ->where('package_type', $packageType)
            ->where('child_count', $childCount)
            ->first();

        return $pricing ? (float) $pricing->price_per_night : null;
    }
}
