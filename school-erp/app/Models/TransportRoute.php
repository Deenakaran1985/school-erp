<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// ── TransportRoute ─────────────────────────────────────
class TransportRoute extends Model
{
    protected $fillable = [
        'route_name', 'route_number', 'stops',
        'pickup_start_time', 'drop_end_time',
        'monthly_fee', 'is_active',
    ];

    protected function casts(): array
    {
        return ['stops' => 'array', 'is_active' => 'boolean']; // JSON → array auto
    }

    public function vehicles()         { return $this->hasMany(Vehicle::class, 'route_id'); }
    public function studentTransports() { return $this->hasMany(StudentTransport::class, 'route_id'); }
}
