<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'vehicle_id', 'workshop_id',
        'service_date', 'issue_description', 'repair_details',
        'labor_cost', 'parts_cost', 'total_cost',
        'mileage_at_service', 'next_service_date',
        'technician_name', 'status',
    ];

    protected $casts = [
        'service_date'      => 'date',
        'next_service_date' => 'date',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function workshop()
    {
        return $this->belongsTo(Workshop::class);
    }

    public function serviceParts()
    {
        return $this->hasMany(ServicePart::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }


    public function parts()
    {
        return $this->belongsToMany(Part::class, 'service_parts')
                    ->withPivot('quantity', 'unit_price', 'total_price')
                    ->withTimestamps();
    }

    // Recalculate costs (called when triggers are not available)
    public function recalculateCosts(): void
    {
        $this->parts_cost = $this->serviceParts()->sum('total_price');
        $this->total_cost = $this->labor_cost + $this->parts_cost;
        $this->save();
    }
}
