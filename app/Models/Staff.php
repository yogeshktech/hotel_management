<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Staff extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $table = 'staff';

    protected $guard_name = 'staff';

    public function getDefaultGuardName(): string
    {
        return 'staff';
    }

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar', 'role',
        'department', 'is_active', 'email_verified_at', 'last_login_at',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function homestays()
    {
        return $this->hasMany(Homestay::class, 'staff_id');
    }

    public function vendorProfile()
    {
        return $this->hasOne(VendorProfile::class, 'staff_id');
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class, 'staff_id');
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
