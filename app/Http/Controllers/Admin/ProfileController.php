<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $staff = auth('staff')->user();

        return view('admin.profile.edit', compact('staff'));
    }

    public function update(Request $request)
    {
        $staff = auth('staff')->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff,email,' . $staff->id,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $staff->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'department' => $validated['department'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $staff->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated.');
    }
}
