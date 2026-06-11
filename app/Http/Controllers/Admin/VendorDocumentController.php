<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorDocument;
use App\Models\VendorProfile;
use Illuminate\Http\Request;

class VendorDocumentController extends Controller
{
    public function approve(VendorProfile $vendor, VendorDocument $document)
    {
        abort_if($document->vendor_profile_id !== $vendor->id, 404);

        $document->update(['status' => 'approved', 'rejection_reason' => null]);

        return back()->with('success', $document->type_label . ' approved.');
    }

    public function reject(Request $request, VendorProfile $vendor, VendorDocument $document)
    {
        abort_if($document->vendor_profile_id !== $vendor->id, 404);

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', $document->type_label . ' rejected.');
    }
}
