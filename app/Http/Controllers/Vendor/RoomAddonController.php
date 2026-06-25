<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Homestay;
use App\Models\Room;
use App\Models\RoomAddon;
use App\Support\RoomAddonCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomAddonController extends VendorController
{
    public function updateAddons(Request $request, Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        $validated = $request->validate([
            'addons' => 'required|array',
            'addons.*.id' => 'required|exists:room_addons,id',
            'addons.*.name' => 'required|string|max:100',
            'addons.*.price' => 'required|numeric|min:0',
            'addons.*.charge_type' => 'required|in:per_night,per_stay,per_guest_per_night',
            'addons.*.is_included_in_package' => 'nullable|boolean',
            'addons.*.is_active' => 'nullable|boolean',
        ]);

        foreach ($validated['addons'] as $row) {
            $addon = RoomAddon::where('room_id', $room->id)->findOrFail($row['id']);
            $addon->update([
                'name' => $row['name'],
                'price' => $row['price'],
                'charge_type' => $row['charge_type'],
                'is_included_in_package' => ! empty($row['is_included_in_package']),
                'is_active' => ! empty($row['is_active']),
            ]);
        }

        return back()->with('success', 'Room add-ons updated.');
    }

    public function store(Request $request, Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'charge_type' => 'required|in:per_night,per_stay,per_guest_per_night',
            'is_included_in_package' => 'nullable|boolean',
        ]);

        $slug = Str::slug($validated['name']) . '-' . time();

        $room->addons()->create([
            'slug' => $slug,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'charge_type' => $validated['charge_type'],
            'is_included_in_package' => $request->boolean('is_included_in_package'),
            'is_active' => true,
            'sort_order' => ($room->addons()->max('sort_order') ?? 0) + 1,
        ]);

        return back()->with('success', 'Custom add-on added.');
    }

    public function resetDefaults(Homestay $property, Room $room)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id, 404);

        RoomAddonCatalog::syncForRoom($room, true);

        return back()->with('success', 'Add-ons reset to defaults.');
    }

    public function destroy(Homestay $property, Room $room, RoomAddon $addon)
    {
        $this->ensureOwnProperty($property);
        abort_if($room->homestay_id !== $property->id || $addon->room_id !== $room->id, 404);

        $addon->delete();

        return back()->with('success', 'Add-on removed.');
    }
}
