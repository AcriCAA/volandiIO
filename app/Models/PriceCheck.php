<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriceCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'flight_alert_id',
        'price',
        'currency',
        'carrier_code',
        'raw_offer',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'raw_offer' => 'array',
        ];
    }

    public function flightAlert(): BelongsTo
    {
        return $this->belongsTo(FlightAlert::class);
    }
}
