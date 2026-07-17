<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkshopHour extends Model
{
    protected $fillable = [
        'workshop_id', 'day_of_week', 'open_time', 'close_time', 'is_closed',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    public static array $dayNames = [
        0 => 'Monday',
        1 => 'Tuesday',
        2 => 'Wednesday',
        3 => 'Thursday',
        4 => 'Friday',
        5 => 'Saturday',
        6 => 'Sunday',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::$dayNames[$this->day_of_week] ?? 'Unknown';
    }

    public function getDisplayHoursAttribute(): string
    {
        if ($this->is_closed) return 'Closed';
        if (!$this->open_time || !$this->close_time) return 'Hours not set';
        return date('g:i A', strtotime($this->open_time)) . ' – ' . date('g:i A', strtotime($this->close_time));
    }
}
