<?php

namespace App\Http\Controllers\Vendor;

use App\Models\VendorDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends VendorController
{
    public function index()
    {
        $profile = $this->ensureProfile();
        $documents = $profile->documents()->latest()->get()->keyBy('document_type');
        $documentTypes = VendorDocument::TYPES;

        return view('vendor.documents.index', compact('profile', 'documents', 'documentTypes'));
    }

    public function store(Request $request)
    {
        $profile = $this->ensureProfile();

        $validated = $request->validate([
            'document_type' => 'required|in:' . implode(',', array_keys(VendorDocument::TYPES)),
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $existing = $profile->documents()->where('document_type', $validated['document_type'])->first();
        if ($existing) {
            Storage::disk('public')->delete($existing->file_path);
            $existing->delete();
        }

        $file = $request->file('document');
        $path = $file->store('vendor-documents/' . $profile->id, 'public');

        $profile->documents()->create([
            'document_type' => $validated['document_type'],
            'file_path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'status' => 'pending',
        ]);

        return redirect()->route('vendor.documents.index')
            ->with('success', VendorDocument::TYPES[$validated['document_type']] . ' uploaded successfully.');
    }

    public function destroy(VendorDocument $document)
    {
        $profile = $this->ensureProfile();

        if ($document->vendor_profile_id !== $profile->id) {
            abort(403);
        }

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return redirect()->route('vendor.documents.index')
            ->with('success', 'Document removed.');
    }
}
