<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorProfile extends Model
{
    protected $fillable = [
        'staff_id',
        'business_name',
        'contact_phone',
        'contact_email',
        'address',
        'city',
        'state',
        'pincode',
        'gst_number',
        'description',
        'status',
        'rejection_reason',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function user(): BelongsTo
    {
        return $this->staff();
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }

    public function documents()
    {
        return $this->hasMany(VendorDocument::class);
    }

    public function isProfileComplete(): bool
    {
        return filled($this->business_name)
            && filled($this->contact_phone)
            && filled($this->contact_email)
            && filled($this->address)
            && filled($this->city)
            && filled($this->state)
            && filled($this->pincode)
            && filled($this->description);
    }

    public function hasRequiredDocuments(): bool
    {
        $uploaded = $this->documents()->pluck('document_type');

        return $uploaded->contains('pan_card') && $uploaded->contains('id_proof');
    }

    public function canManageProperties(): bool
    {
        return $this->status === 'approved'
            && $this->isProfileComplete()
            && $this->hasRequiredDocuments();
    }

    public function onboardingSteps(): array
    {
        return [
            'approved' => $this->status === 'approved',
            'profile' => $this->isProfileComplete(),
            'documents' => $this->hasRequiredDocuments(),
            'locations' => Location::exists(),
            'properties' => $this->staff?->homestays()->exists() ?? false,
            'rooms' => $this->staff?->homestays()->whereHas('rooms')->exists() ?? false,
        ];
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
