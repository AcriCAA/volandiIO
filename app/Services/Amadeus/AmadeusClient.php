<?php

namespace App\Services\Amadeus;

use App\Exceptions\FlightPriceLookupException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Thin client for the Amadeus for Developers "Self-Service" APIs.
 *
 * @see https://developers.amadeus.com/self-service/category/flights/api-doc/flight-offers-search
 */
class AmadeusClient
{
    private const TOKEN_CACHE_KEY = 'amadeus.access_token';

    public function __construct(
        private readonly string $clientId,
        private readonly string $clientSecret,
        private readonly string $baseUrl,
    ) {}

    /**
     * Search flight offers and return the raw decoded response.
     *
     * @return array<string, mixed>
     */
    public function searchFlightOffers(array $params): array
    {
        $response = Http::baseUrl($this->baseUrl)
            ->withToken($this->getAccessToken())
            ->acceptJson()
            ->get('/v2/shopping/flight-offers', $params);

        if ($response->failed()) {
            throw new FlightPriceLookupException(
                "Amadeus flight offers search failed with status {$response->status()}: {$response->body()}"
            );
        }

        return $response->json();
    }

    private function getAccessToken(): string
    {
        return Cache::remember(self::TOKEN_CACHE_KEY, now()->addMinutes(25), function () {
            $response = Http::asForm()
                ->baseUrl($this->baseUrl)
                ->post('/v1/security/oauth2/token', [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                ]);

            if ($response->failed()) {
                throw new FlightPriceLookupException(
                    "Amadeus authentication failed with status {$response->status()}: {$response->body()}"
                );
            }

            return $response->json('access_token');
        });
    }
}
