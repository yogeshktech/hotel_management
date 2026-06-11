<?php

namespace App\Http\Controllers\Vendor;

use Illuminate\Http\Request;

class ProfileController extends VendorController
{
    public function edit()
    {
        $profile = $this->ensureProfile();

        return view('vendor.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $profile = $this->ensureProfile();

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'required|email|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|string|max:10',
            'gst_number' => 'nullable|string|max:20',
            'description' => 'required|string|max:2000',
        ]);

        $profile->update($validated);

        return redirect()->route('vendor.profile.edit')
            ->with('success', 'Vendor profile updated successfully.');
    }
}
