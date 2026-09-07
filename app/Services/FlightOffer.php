<?php

namespace App\Services;

class FlightOffer
{
    public function __construct(
        public readonly float $price,
        public readonly string $currency,
        public readonly ?string $carrierCode,
        public readonly array $raw,
    ) {}
}
