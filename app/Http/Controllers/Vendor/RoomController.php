<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Homestay;
use App\Models\Room;
use App\Support\RoomAddonCatalog;
use App\Support\RoomPackagePricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomController extends VendorController
{
    public function create(Homestay $property)
    {
        $this->ensureOwnProperty($property);

        return view('vendor.rooms.create', compact('property'));
    }

    public function store(Request $request, Homestay $property)
    {
        $this->ensureOwnProperty($property);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'bed_count' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'total_units' => 'required|integer|min:1',
        ]);

        $room = $property->rooms()->create([
            ...$validated,
            'status' => 'active',
            'amenities' => [],
        ]);

        RoomPackagePricing::syncForRoom($room, (float) $validated['price_per_night']);
        RoomAddonCatalog::syncForRoom($room);

        return redirect()->route('vendor.rooms.edit', [$property, $room])
            ->with('success', 'Room added. Now upload room gallery photos below.');
    }

    public function edit(Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        $room->load(['pricings', 'images', 'seasons', 'addons']);

        return view('vendor.rooms.edit', compact('property', 'room'));
    }

    public function update(Request $request, Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'room_type' => 'required|string|max:100',
            'description' => 'nullable|string',
            'capacity' => 'required|integer|min:1',
            'bed_count' => 'required|integer|min:1',
            'price_per_night' => 'required|numeric|min:0',
            'total_units' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,draft',
        ]);

        $room->update($validated);

        if ($request->boolean('refresh_pricing')) {
            RoomPackagePricing::syncForRoom($room, (float) $validated['price_per_night']);
        }

        return redirect()->route('vendor.properties.show', $property)
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        if ($room->bookings()->blocking()->exists()) {
            return back()->with('error', 'Cannot delete room with active bookings.');
        }

        foreach ($room->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        $room->images()->delete();
        $room->pricings()->delete();
        $room->delete();

        return redirect()->route('vendor.properties.show', $property)
            ->with('success', 'Room deleted.');
    }
}
