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

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
