<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlightAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'origin',
        'destination',
        'departure_date',
        'return_date',
        'adults',
        'cabin_class',
        'target_price',
        'currency',
        'last_price',
        'last_checked_at',
        'triggered_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'departure_date' => 'date',
            'return_date' => 'date',
            'target_price' => 'decimal:2',
            'last_price' => 'decimal:2',
            'last_checked_at' => 'datetime',
            'triggered_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function priceChecks(): HasMany
    {
        return $this->hasMany(PriceCheck::class);
    }

    public function hasMetTargetPrice(float $price): bool
    {
        return $price <= (float) $this->target_price;
    }
}
