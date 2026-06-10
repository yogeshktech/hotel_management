<?php

// app/Models/WaitingList.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'homestay_id', 'user_id', 'desired_check_in',
        'desired_check_out', 'guests', 'message', 'status'
    ];

    protected $dates = ['desired_check_in', 'desired_check_out'];

    public function homestay()
    {
        return $this->belongsTo(Homestay::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
