<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomPricingSeason extends Model
{
    protected $fillable = [
        'room_id',
        'name',
        'start_date',
        'end_date',
        'price_multiplier',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'price_multiplier' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function covers(Carbon $date): bool
    {
        $day = $date->toDateString();

        return $day >= $this->start_date->toDateString()
            && $day <= $this->end_date->toDateString();
    }

    public function getMultiplierLabelAttribute(): string
    {
        $pct = round(((float) $this->price_multiplier - 1) * 100);

        if ($pct > 0) {
            return "+{$pct}%";
        }

        if ($pct < 0) {
            return "{$pct}%";
        }

        return 'Normal rate';
    }
}
