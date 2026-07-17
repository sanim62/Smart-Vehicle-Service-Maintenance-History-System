<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePart extends Model
{
    protected $fillable = [
        'service_id', 'part_id', 'quantity', 'unit_price', 'total_price',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function part()
    {
        return $this->belongsTo(Part::class);
    }
}
