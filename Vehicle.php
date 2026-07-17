<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'make',
        'model',
        'year',
        'registration_number',
        'chassis_number',
        'color',
        'fuel_type',
        'mileage',
        'status',
    ];

    // ─── Relationships ───────────────────────────────────────────
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // ─── Maintenance Reminder Logic ──────────────────────────────
    public function getMaintenanceStatusAttribute(): string
    {
        $lastService = $this->services()->latest('service_date')->first();

        if (!$lastService) {
            return 'No Service Yet';
        }

        // If next_service_date is set, use that
        if ($lastService->next_service_date) {
            $daysUntil = now()->diffInDays($lastService->next_service_date, false);
            if ($daysUntil < 0)  return 'Overdue';
            if ($daysUntil <= 7) return 'Due Soon';
            return 'Normal';
        }

        // Default: remind every 90 days
        $daysSince = $lastService->service_date
            ? now()->diffInDays(Carbon::parse($lastService->service_date))
            : 999;

        if ($daysSince >= 90) return 'Overdue';
        if ($daysSince >= 75) return 'Due Soon';
        return 'Normal';
    }

    public function getLastServiceDateAttribute()
    {
        return $this->services()->latest('service_date')->value('service_date');
    }

    public function getTotalSpentAttribute(): float
    {
        return $this->services()->sum('total_cost');
    }
}
