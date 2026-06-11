<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Homestay;
use App\Models\Staff;
use App\Models\VendorProfile;

abstract class VendorController extends Controller
{
    protected function staff(): Staff
    {
        return auth('staff')->user();
    }

    protected function profile(): ?VendorProfile
    {
        return $this->staff()->vendorProfile;
    }

    protected function ensureProfile(): VendorProfile
    {
        $profile = $this->profile();

        if (! $profile) {
            abort(404, 'Vendor profile not found. Contact admin.');
        }

        return $profile;
    }

    protected function ensureOwnProperty(Homestay $property): Homestay
    {
        if ($property->staff_id !== $this->staff()->id) {
            abort(403);
        }

        return $property;
    }

    protected function ensureCanManageProperties(): void
    {
        $profile = $this->ensureProfile();

        if (! $profile->canManageProperties()) {
            abort(403, 'Complete your profile and upload required documents before managing properties.');
        }
    }
}
