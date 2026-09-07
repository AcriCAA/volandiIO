<?php

namespace App\Providers;

use App\Services\Amadeus\AmadeusClient;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AmadeusClient::class, function () {
            return new AmadeusClient(
                clientId: (string) config('services.amadeus.client_id'),
                clientSecret: (string) config('services.amadeus.client_secret'),
                baseUrl: (string) config('services.amadeus.base_url'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
