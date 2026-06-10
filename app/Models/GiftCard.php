<?php

// app/Models/GiftCard.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'amount', 'balance', 'sender_user_id',
        'receiver_user_id', 'expiry_date', 'active'
    ];

    protected $dates = ['expiry_date'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }
}