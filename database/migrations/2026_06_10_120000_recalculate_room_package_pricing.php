<?php

use App\Models\Room;
use App\Support\RoomPackagePricing;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Room::query()->each(function (Room $room) {
            RoomPackagePricing::syncForRoom($room, (float) $room->price_per_night);
        });
    }

    public function down(): void
    {
        // Previous multipliers differed; re-run up() after deploy if rollback is needed.
    }
};
