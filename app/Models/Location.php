<?php

// app/Models/Location.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'country', 'province', 'city',
        'description', 'latitude', 'longitude', 'homestays_count'
    ];

    public function homestays()
    {
        return $this->hasMany(Homestay::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function activeHomestays()
    {
        return $this->homestays()->active();
    }
}
