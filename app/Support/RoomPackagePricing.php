<?php

namespace App\Support;

use App\Models\Room;
use App\Models\RoomPricing;

class RoomPackagePricing
{
    /**
     * Room list price is the couple (2 guests) rate. Single adult gets a discount; family/child add-ons cost more.
     *
     * @return list<array{package_type: string, child_count: int, adult_count: int, multiplier: float}>
     */
    public static function packages(): array
    {
        return [
            ['package_type' => 'adult', 'child_count' => 0, 'adult_count' => 1, 'multiplier' => 0.75],
            ['package_type' => 'couple', 'child_count' => 0, 'adult_count' => 2, 'multiplier' => 1.0],
            ['package_type' => 'family', 'child_count' => 1, 'adult_count' => 2, 'multiplier' => 1.25],
            ['package_type' => 'family', 'child_count' => 2, 'adult_count' => 2, 'multiplier' => 1.45],
            ['package_type' => 'family', 'child_count' => 3, 'adult_count' => 2, 'multiplier' => 1.65],
            ['package_type' => 'family', 'child_count' => 4, 'adult_count' => 2, 'multiplier' => 1.85],
            ['package_type' => 'child', 'child_count' => 1, 'adult_count' => 0, 'multiplier' => 0.35],
            ['package_type' => 'child', 'child_count' => 2, 'adult_count' => 0, 'multiplier' => 0.55],
            ['package_type' => 'child', 'child_count' => 3, 'adult_count' => 0, 'multiplier' => 0.75],
            ['package_type' => 'child', 'child_count' => 4, 'adult_count' => 0, 'multiplier' => 0.95],
        ];
    }

    public static function syncForRoom(Room $room, float $basePerNight, bool $replaceExisting = true): void
    {
        if ($replaceExisting) {
            $room->pricings()->delete();
        }

        foreach (self::packages() as $package) {
            RoomPricing::updateOrCreate(
                [
                    'room_id' => $room->id,
                    'package_type' => $package['package_type'],
                    'child_count' => $package['child_count'],
                ],
                [
                    'adult_count' => $package['adult_count'],
                    'price_per_night' => round($basePerNight * $package['multiplier']),
                ]
            );
        }
    }
}
