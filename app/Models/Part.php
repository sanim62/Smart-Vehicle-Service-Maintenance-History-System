<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Part extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'part_number', 'category', 'unit_price', 'unit',
    ];

    public function serviceParts()
    {
        return $this->hasMany(ServicePart::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'service_parts')
                    ->withPivot('quantity', 'unit_price', 'total_price');
    }
}
