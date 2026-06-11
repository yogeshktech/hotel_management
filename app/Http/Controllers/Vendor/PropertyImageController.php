<?php

namespace App\Http\Controllers\Vendor;

use App\Models\Homestay;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PropertyImageController extends VendorController
{
    public function store(Request $request, Homestay $property)
    {
        $this->ensureOwnProperty($property);

        $request->validate([
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        foreach ($request->file('images') as $file) {
            $path = $file->store('property-images/' . $property->id, 'public');
            $property->images()->create([
                'path' => $path,
                'is_primary' => $property->images()->count() === 0,
                'sort_order' => $property->images()->count(),
            ]);
        }

        return back()->with('success', 'Property images uploaded successfully.');
    }

    public function destroy(Homestay $property, PropertyImage $image)
    {
        $this->ensureOwnProperty($property);
        abort_if($image->homestay_id !== $property->id, 404);

        Storage::disk('public')->delete($image->path);
        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $property->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed.');
    }

    public function setPrimary(Homestay $property, PropertyImage $image)
    {
        $this->ensureOwnProperty($property);
        abort_if($image->homestay_id !== $property->id, 404);

        $property->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Primary image updated.');
    }
}
