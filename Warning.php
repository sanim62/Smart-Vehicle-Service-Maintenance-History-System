<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'complaint_id',
        'admin_id',
        'subject',
        'warning_message',
        'severity',
        'status',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function severityBadgeClass(): string
    {
        return match($this->severity) {
            'low'      => 'bg-info',
            'medium'   => 'bg-warning text-dark',
            'high'     => 'bg-danger',
            'critical' => 'bg-dark text-white border border-danger',
            default    => 'bg-secondary',
        };
    }
}
