<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'vehicle_id', 'workshop_id',
        'booking_date', 'booking_time', 'service_type',
        'problem_description', 'status', 'notes', 'estimated_cost',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function service()
    {
        return $this->hasOne(Service::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'     => 'warning',
            'approved'    => 'info',
            'in_progress' => 'primary',
            'completed'   => 'success',
            'cancelled'   => 'danger',
            default       => 'secondary',
        };
    }
}
