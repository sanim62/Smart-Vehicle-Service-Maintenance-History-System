<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'workshop_id', 'booking_id', 'rating', 'comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function starHtml(): string
    {
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $this->rating
                ? '<i class="bi bi-star-fill text-warning"></i>'
                : '<i class="bi bi-star text-muted"></i>';
        }
        return $html;
    }
}
