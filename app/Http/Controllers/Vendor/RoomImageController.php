<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Homestay;
use App\Models\Room;
use App\Models\RoomImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomImageController extends VendorController
{
    public function store(Request $request, Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        $request->validate([
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        foreach ($request->file('images') as $file) {
            $path = $file->store('room-images/' . $room->id, 'public');
            $room->images()->create([
                'path' => $path,
                'is_primary' => $room->images()->count() === 0,
                'sort_order' => $room->images()->count(),
            ]);
        }

        return back()->with('success', 'Room images uploaded successfully.');
    }

    public function destroy(Homestay $property, Room $room, RoomImage $image)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id || $image->room_id !== $room->id, 404);

        Storage::disk('public')->delete($image->path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $room->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Room image removed.');
    }

    public function setPrimary(Homestay $property, Room $room, RoomImage $image)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id || $image->room_id !== $room->id, 404);

        $room->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary room image updated.');
    }
}
