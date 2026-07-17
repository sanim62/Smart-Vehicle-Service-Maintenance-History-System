<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'user_id', 'workshop_id', 'subject', 'message', 'type',
        'status', 'admin_reply', 'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function warning()
    {
        return $this->hasOne(Warning::class);
    }


    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'open'      => 'badge-open',
            'in_review' => 'badge-in-review',
            'resolved'  => 'badge-resolved',
            'closed'    => 'badge-closed',
            default     => 'bg-secondary',
        };
    }

    public function typeBadgeClass(): string
    {
        return match($this->type) {
            'complaint' => 'text-bg-danger',
            'demand'    => 'text-bg-warning',
            'request'   => 'text-bg-info',
            'feedback'  => 'text-bg-success',
            default     => 'text-bg-secondary',
        };
    }
}
