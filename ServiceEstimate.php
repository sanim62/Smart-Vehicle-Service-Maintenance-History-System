<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceEstimate extends Model
{
    protected $fillable = [
        'workshop_id', 'service_type', 'min_price', 'max_price', 'duration_hours', 'notes',
    ];

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function priceRange(): string
    {
        if ($this->min_price == $this->max_price) {
            return '৳' . number_format($this->min_price);
        }
        return '৳' . number_format($this->min_price) . ' – ৳' . number_format($this->max_price);
    }
}
