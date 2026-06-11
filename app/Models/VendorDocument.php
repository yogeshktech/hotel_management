<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class VendorDocument extends Model
{
    public const TYPES = [
        'pan_card' => 'PAN Card',
        'id_proof' => 'ID Proof (Aadhaar/Passport)',
        'gst_certificate' => 'GST Certificate',
        'business_license' => 'Business License',
        'bank_proof' => 'Bank Account Proof',
    ];

    protected $fillable = [
        'vendor_profile_id',
        'document_type',
        'file_path',
        'original_name',
        'status',
        'rejection_reason',
    ];

    public function vendorProfile(): BelongsTo
    {
        return $this->belongsTo(VendorProfile::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->document_type] ?? $this->document_type;
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function isImage(): bool
    {
        $ext = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));

        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    }
}
