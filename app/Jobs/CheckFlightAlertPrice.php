<?php

namespace App\Jobs;

use App\Exceptions\FlightPriceLookupException;
use App\Models\FlightAlert;
use App\Notifications\FlightPriceAlertNotification;
use App\Services\FlightPriceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CheckFlightAlertPrice implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly FlightAlert $alert) {}

    public function handle(FlightPriceService $flightPriceService): void
    {
        if (! $this->alert->is_active) {
            return;
        }

        try {
            $offer = $flightPriceService->findCheapestOffer($this->alert);
        } catch (FlightPriceLookupException $exception) {
            Log::warning('Flight price lookup failed', [
                'flight_alert_id' => $this->alert->id,
                'message' => $exception->getMessage(),
            ]);

            return;
        }

        $this->alert->priceChecks()->create([
            'price' => $offer->price,
            'currency' => $offer->currency,
            'carrier_code' => $offer->carrierCode,
            'raw_offer' => $offer->raw,
        ]);

        $this->alert->forceFill([
            'last_price' => $offer->price,
            'last_checked_at' => now(),
        ])->save();

        if ($this->alert->hasMetTargetPrice($offer->price) && ! $this->alert->triggered_at) {
            $this->alert->user->notify(new FlightPriceAlertNotification($this->alert, $offer->price));

            $this->alert->forceFill(['triggered_at' => now()])->save();
        }
    }
}
