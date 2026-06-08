<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'route_id', 'vehicle_number', 'vehicle_type', 'make_model',
        'capacity', 'driver_id', 'insurance_expiry', 'fitness_expiry',
        'permit_expiry', 'last_service_date', 'status',
    ];

    protected function casts(): array
    {
        return [
            'insurance_expiry'  => 'date',
            'fitness_expiry'    => 'date',
            'permit_expiry'     => 'date',
            'last_service_date' => 'date',
        ];
    }

    public function route()  { return $this->belongsTo(TransportRoute::class); }
    public function driver() { return $this->belongsTo(Staff::class, 'driver_id'); }

    public function getIsInsuranceExpiredAttribute(): bool
    {
        return $this->insurance_expiry && $this->insurance_expiry->isPast();
    }
}