<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Homestay;
use App\Models\Room;
use App\Models\RoomPricing;
use Illuminate\Http\Request;

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

        $this->seedDefaultPricing($room, (float) $validated['price_per_night']);

        return redirect()->route('vendor.rooms.edit', [$property, $room])
            ->with('success', 'Room added. Now upload room gallery photos below.');
    }

    public function edit(Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        $room->load(['pricings', 'images']);

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
            $room->pricings()->delete();
            $this->seedDefaultPricing($room, (float) $validated['price_per_night']);
        }

        return redirect()->route('vendor.properties.show', $property)
            ->with('success', 'Room updated successfully.');
    }

    public function destroy(Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        if ($room->bookings()->exists()) {
            return back()->with('error', 'Cannot delete room with existing bookings.');
        }

        $room->pricings()->delete();
        $room->delete();

        return redirect()->route('vendor.properties.show', $property)
            ->with('success', 'Room deleted.');
    }

    private function seedDefaultPricing(Room $room, float $base): void
    {
        $packages = [
            ['package_type' => 'adult', 'child_count' => 0, 'adult_count' => 1, 'multiplier' => 1.0],
            ['package_type' => 'couple', 'child_count' => 0, 'adult_count' => 2, 'multiplier' => 1.6],
            ['package_type' => 'family', 'child_count' => 1, 'adult_count' => 2, 'multiplier' => 2.0],
            ['package_type' => 'family', 'child_count' => 2, 'adult_count' => 2, 'multiplier' => 2.3],
            ['package_type' => 'child', 'child_count' => 1, 'adult_count' => 0, 'multiplier' => 0.4],
        ];

        foreach ($packages as $p) {
            RoomPricing::create([
                'room_id' => $room->id,
                'package_type' => $p['package_type'],
                'child_count' => $p['child_count'],
                'adult_count' => $p['adult_count'],
                'price_per_night' => round($base * $p['multiplier']),
            ]);
        }
    }
}
