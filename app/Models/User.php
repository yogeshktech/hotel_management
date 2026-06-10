<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;  //  ← ADD THIS LINE (import)

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;                           //  ← ADD THIS LINE (use the trait)

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',                    // 'guest', 'host', 'admin', 'franchisee' etc.
        'email_verified_at',
        'phone_verified_at',
        'is_active',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'last_login_at'     => 'datetime',
        ];
    }

    // ────────────────────────────────────────────────────────────────
    // RELATIONSHIPS (unchanged)
    // ────────────────────────────────────────────────────────────────

    public function homestays()
    {
        return $this->hasMany(Homestay::class, 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function sentGiftCards()
    {
        return $this->hasMany(GiftCard::class, 'sender_user_id');
    }

    public function receivedGiftCards()
    {
        return $this->hasMany(GiftCard::class, 'receiver_user_id');
    }

    public function waitingListEntries()
    {
        return $this->hasMany(WaitingList::class, 'user_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'user_id');
    }

    public function vendorProfile()
    {
        return $this->hasOne(VendorProfile::class);
    }

    // ────────────────────────────────────────────────────────────────
    // HELPER / SCOPE METHODS (unchanged)
    // ────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeHosts($query)
    {
        return $query->whereHas('homestays');
    }

    public function isHost(): bool
    {
        return $this->homestays()->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isVendor(): bool
    {
        return $this->hasRole('vendor');
    }

    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin_staff']);
    }
}