<?php

// app/Models/PromoCode.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value', 'min_amount',
        'max_uses', 'used_count', 'valid_from',
        'valid_until', 'active'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'active' => 'boolean',
    ];
}
