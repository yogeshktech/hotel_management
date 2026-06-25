<?php

namespace App\Support;

use App\Models\Room;
use App\Models\RoomAddon;

class RoomAddonCatalog
{
    /**
     * @return list<array{slug: string, name: string, price: float, charge_type: string, is_included_in_package: bool, sort_order: int}>
     */
    public static function defaults(): array
    {
        return [
            ['slug' => 'wifi', 'name' => 'WiFi', 'price' => 0, 'charge_type' => 'per_stay', 'is_included_in_package' => true, 'sort_order' => 1],
            ['slug' => 'ac', 'name' => 'Air Conditioning', 'price' => 300, 'charge_type' => 'per_night', 'is_included_in_package' => true, 'sort_order' => 2],
            ['slug' => 'heater', 'name' => 'Room Heater', 'price' => 200, 'charge_type' => 'per_night', 'is_included_in_package' => true, 'sort_order' => 3],
            ['slug' => 'breakfast', 'name' => 'Breakfast', 'price' => 250, 'charge_type' => 'per_guest_per_night', 'is_included_in_package' => true, 'sort_order' => 4],
            ['slug' => 'lunch', 'name' => 'Lunch', 'price' => 350, 'charge_type' => 'per_guest_per_night', 'is_included_in_package' => true, 'sort_order' => 5],
            ['slug' => 'dinner', 'name' => 'Dinner', 'price' => 400, 'charge_type' => 'per_guest_per_night', 'is_included_in_package' => true, 'sort_order' => 6],
            ['slug' => 'parking', 'name' => 'Parking', 'price' => 100, 'charge_type' => 'per_night', 'is_included_in_package' => false, 'sort_order' => 7],
            ['slug' => 'laundry', 'name' => 'Laundry', 'price' => 150, 'charge_type' => 'per_stay', 'is_included_in_package' => false, 'sort_order' => 8],
            ['slug' => 'geyser', 'name' => 'Geyser / Hot Water', 'price' => 0, 'charge_type' => 'per_stay', 'is_included_in_package' => true, 'sort_order' => 9],
            ['slug' => 'tv', 'name' => 'TV / Entertainment', 'price' => 0, 'charge_type' => 'per_stay', 'is_included_in_package' => true, 'sort_order' => 10],
        ];
    }

    public static function syncForRoom(Room $room, bool $replaceExisting = false): void
    {
        if ($replaceExisting) {
            $room->addons()->delete();
        }

        foreach (self::defaults() as $addon) {
            RoomAddon::updateOrCreate(
                ['room_id' => $room->id, 'slug' => $addon['slug']],
                $addon
            );
        }
    }
}
