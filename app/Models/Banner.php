<?php

// app/Models/Banner.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'image', 'button_text',
        'button_url', 'placement', 'start_date',
        'end_date', 'active', 'order'
    ];

    protected $dates = ['start_date', 'end_date'];
}