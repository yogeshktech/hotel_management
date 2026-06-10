<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $customer = auth('customer')->user();

        return view('customer.profile.edit', compact('customer'));
    }

    public function update(Request $request)
    {
        $customer = auth('customer')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $customer->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $customer->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('customer.profile.edit')->with('success', 'Profile updated successfully.');
    }
}
