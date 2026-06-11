<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'homestay_id', 'customer_id', 'room_id', 'booking_channel', 'guest_package',
        'adults_count', 'children_count', 'check_in', 'check_out', 'guests',
        'total_price', 'base_price', 'cleaning_fee', 'service_fee', 'currency',
        'payment_method', 'payment_status', 'status', 'guest_notes', 'promo_code',
        'promo_discount', 'booked_at', 'checked_in_at', 'checked_out_at',
        'vacant_from', 'created_by_staff_id', 'booking_reference',
    ];

    protected function casts(): array
    {
        return [
            'check_in' => 'date',
            'check_out' => 'date',
            'vacant_from' => 'date',
            'booked_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (! $booking->booking_reference) {
                $booking->booking_reference = 'BK' . strtoupper(uniqid());
            }
            if (! $booking->booked_at) {
                $booking->booked_at = now();
            }
            if (! $booking->vacant_from && $booking->check_out) {
                $booking->vacant_from = $booking->check_out;
            }
        });
    }

    public function homestay()
    {
        return $this->belongsTo(Homestay::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function createdByStaff()
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id');
    }

    public function review()
    {
        return $this->hasOne(BookingReview::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class, 'promo_code', 'code');
    }

    public function getNightsAttribute(): int
    {
        if (! $this->check_in || ! $this->check_out) {
            return 0;
        }
        return max(1, Carbon::parse($this->check_in)->diffInDays($this->check_out));
    }

    public function getIsOccupiedAttribute(): bool
    {
        return in_array($this->status, ['confirmed', 'checked_in'])
            && $this->check_in <= now()->toDateString()
            && $this->check_out > now()->toDateString();
    }

    public function getVacantOnAttribute(): ?string
    {
        return $this->vacant_from?->format('d M Y') ?? $this->check_out?->format('d M Y');
    }

    public function scopeOnline($query)
    {
        return $query->where('booking_channel', 'online');
    }

    public function scopeOffline($query)
    {
        return $query->where('booking_channel', 'offline');
    }

    public function scopeBlocking($query)
    {
        return $query->whereNotIn('status', ['cancelled', 'rejected']);
    }

    public function getRouteKeyName(): string
    {
        return 'booking_reference';
    }
}
