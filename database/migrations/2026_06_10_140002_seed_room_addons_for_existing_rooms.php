<?php

use App\Models\Room;
use App\Support\RoomAddonCatalog;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Room::query()->each(function (Room $room) {
            RoomAddonCatalog::syncForRoom($room);
        });
    }

    public function down(): void
    {
        //
    }
};
