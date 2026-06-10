<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomPricing extends Model
{
    protected $fillable = [
        'room_id', 'package_type', 'child_count', 'adult_count', 'price_per_night',
    ];

    protected function casts(): array
    {
        return ['price_per_night' => 'decimal:2'];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public static function packageLabel(string $type, int $childCount = 0): string
    {
        return match ($type) {
            'adult' => 'Single Adult',
            'couple' => 'Couple (2 Adults)',
            'family' => 'Family (2 Adults + ' . $childCount . ' Child' . ($childCount > 1 ? 'ren' : '') . ')',
            'child' => $childCount . ' Child' . ($childCount > 1 ? 'ren' : ''),
            default => ucfirst($type),
        };
    }
}
