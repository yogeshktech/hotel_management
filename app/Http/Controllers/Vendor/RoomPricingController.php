<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Homestay;
use App\Models\Room;
use App\Models\RoomPricing;
use App\Models\RoomPricingSeason;
use App\Support\RoomPackagePricing;
use Illuminate\Http\Request;

class RoomPricingController extends VendorController
{
    public function updatePackages(Request $request, Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        $validated = $request->validate([
            'pricings' => 'required|array|min:1',
            'pricings.*.id' => 'required|exists:room_pricings,id',
            'pricings.*.price_per_night' => 'required|numeric|min:0',
        ]);

        foreach ($validated['pricings'] as $row) {
            $pricing = RoomPricing::where('room_id', $room->id)->findOrFail($row['id']);
            $pricing->update(['price_per_night' => $row['price_per_night']]);
        }

        $couple = $room->pricings()->where('package_type', 'couple')->where('child_count', 0)->first();
        if ($couple) {
            $room->update(['price_per_night' => $couple->price_per_night]);
        }

        return back()->with('success', 'Package prices updated successfully.');
    }

    public function resetPackages(Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        RoomPackagePricing::syncForRoom($room, (float) $room->price_per_night);

        return back()->with('success', 'Package prices reset from room base price.');
    }

    public function storeSeason(Request $request, Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price_multiplier' => 'required|numeric|min:0.1|max:5',
            'notes' => 'nullable|string|max:255',
        ]);

        $room->seasons()->create([
            ...$validated,
            'is_active' => true,
        ]);

        return back()->with('success', 'Seasonal pricing added.');
    }

    public function updateSeason(Request $request, Homestay $property, Room $room, RoomPricingSeason $season)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id || $season->room_id !== $room->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price_multiplier' => 'required|numeric|min:0.1|max:5',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string|max:255',
        ]);

        $season->update([
            ...$validated,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Seasonal pricing updated.');
    }

    public function destroySeason(Homestay $property, Room $room, RoomPricingSeason $season)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id || $season->room_id !== $room->id, 404);

        $season->delete();

        return back()->with('success', 'Seasonal pricing removed.');
    }
}
