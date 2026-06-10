<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingReview extends Model
{
    protected $fillable = [
        'booking_id', 'customer_id', 'homestay_id',
        'service_rating', 'food_rating', 'overall_rating', 'comment',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function homestay(): BelongsTo
    {
        return $this->belongsTo(Homestay::class);
    }
}
