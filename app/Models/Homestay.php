<?php

// app/Models/Homestay.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Homestay extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id', 'staff_id', 'title', 'slug', 'description',
        'max_guests', 'bedrooms', 'beds', 'bathrooms',
        'price_per_night', 'cleaning_fee', 'service_fee_percentage',
        'currency', 'amenities', 'house_rules', 'address',
        'latitude', 'longitude', 'status', 'view_count'
    ];

    protected $casts = [
        'amenities' => 'array',
        'house_rules' => 'array',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function owner()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function reviews()
    {
        return $this->hasMany(BookingReview::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
