<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomAddon extends Model
{
    public const CHARGE_TYPES = [
        'per_night' => 'Per night',
        'per_stay' => 'Per stay (once)',
        'per_guest_per_night' => 'Per guest / night',
    ];

    protected $fillable = [
        'room_id',
        'slug',
        'name',
        'price',
        'charge_type',
        'is_included_in_package',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_included_in_package' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function chargeLabel(): string
    {
        return self::CHARGE_TYPES[$this->charge_type] ?? $this->charge_type;
    }

    public function isFree(): bool
    {
        return (float) $this->price <= 0;
    }
}
