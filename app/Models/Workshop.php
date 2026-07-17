<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// ────────────────────────────────────────────────────────────
// Workshop Model
// ────────────────────────────────────────────────────────────
class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'owner_name', 'phone', 'email',
        'address', 'city', 'latitude', 'longitude', 'license_number',
        'service_categories', 'status', 'description', 'bank_account',
        'rating_avg', 'total_reviews', 'is_verified', 'photos',
    ];

    protected $casts = [
        'photos'      => 'array',
        'is_verified' => 'boolean',
    ];

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

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function warnings()
    {
        return $this->hasMany(Warning::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function estimates()
    {
        return $this->hasMany(ServiceEstimate::class);
    }

    public function hours()
    {
        return $this->hasMany(WorkshopHour::class)->orderBy('day_of_week');
    }

    public function getServiceCategoriesListAttribute(): array
    {
        return json_decode($this->service_categories, true) ?? [];
    }

    // Returns true if workshop is currently open based on workshop_hours
    public function isOpenNow(): bool
    {
        $dayOfWeek = (int) now()->format('N') - 1; // 0=Mon, 6=Sun
        $hour = $this->hours()->where('day_of_week', $dayOfWeek)->first();
        if (!$hour || $hour->is_closed || !$hour->open_time || !$hour->close_time) return false;
        $now = now()->format('H:i:s');
        return $now >= $hour->open_time && $now <= $hour->close_time;
    }

    // Recalculates and saves rating_avg and total_reviews from reviews table
    public function updateRatingCache(): void
    {
        $reviews = $this->reviews();
        $this->update([
            'total_reviews' => $reviews->count(),
            'rating_avg'    => round($reviews->avg('rating') ?? 0, 2),
        ]);
    }

    public function starsHtml(): string
    {
        $avg = round($this->rating_avg);
        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $html .= $i <= $avg
                ? '<i class="bi bi-star-fill text-warning"></i>'
                : '<i class="bi bi-star text-muted"></i>';
        }
        return $html;
    }
}
