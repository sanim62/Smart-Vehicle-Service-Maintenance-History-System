<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'national_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─── Role Helpers ───────────────────────────────────────────
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isWorkshop(): bool
    {
        return $this->role === 'workshop';
    }

    // ─── Relationships ───────────────────────────────────────────
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function workshops()
    {
        return $this->hasMany(Workshop::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
