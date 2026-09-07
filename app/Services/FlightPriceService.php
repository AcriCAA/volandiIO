<?php

namespace App\Services;

use App\Exceptions\FlightPriceLookupException;
use App\Models\FlightAlert;
use App\Services\Amadeus\AmadeusClient;

class FlightPriceService
{
    public function __construct(private readonly AmadeusClient $amadeus) {}

    /**
     * Look up live offers for the alert's route/dates and return the cheapest one.
     */
    public function findCheapestOffer(FlightAlert $alert): FlightOffer
    {
        $params = [
            'originLocationCode' => $alert->origin,
            'destinationLocationCode' => $alert->destination,
            'departureDate' => $alert->departure_date->toDateString(),
            'adults' => $alert->adults,
            'travelClass' => $alert->cabin_class,
            'currencyCode' => $alert->currency,
            'max' => 10,
        ];

        if ($alert->return_date) {
            $params['returnDate'] = $alert->return_date->toDateString();
        }

        $response = $this->amadeus->searchFlightOffers($params);

        $offers = $response['data'] ?? [];

        if (empty($offers)) {
            throw new FlightPriceLookupException(
                "No flight offers found for {$alert->origin} -> {$alert->destination} on {$alert->departure_date->toDateString()}"
            );
        }

        $cheapest = collect($offers)->sortBy(fn (array $offer) => (float) $offer['price']['grandTotal'])->first();

        return new FlightOffer(
            price: (float) $cheapest['price']['grandTotal'],
            currency: $cheapest['price']['currency'],
            carrierCode: $cheapest['validatingAirlineCodes'][0] ?? null,
            raw: $cheapest,
        );
    }
}
