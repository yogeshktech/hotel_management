<?php

// app/Models/StaticPage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'content',
        'meta_title', 'meta_description',
        'show_in_footer', 'order'
    ];
}