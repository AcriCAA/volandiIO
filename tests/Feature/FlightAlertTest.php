<?php

namespace Tests\Feature;

use App\Jobs\CheckFlightAlertPrice;
use App\Models\FlightAlert;
use App\Models\User;
use App\Notifications\FlightPriceAlertNotification;
use App\Services\FlightPriceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class FlightAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('flight-alerts.index'))->assertRedirect(route('login'));
    }

    public function test_a_user_can_create_a_flight_alert(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('flight-alerts.store'), [
            'origin' => 'jfk',
            'destination' => 'lax',
            'departure_date' => now()->addMonth()->toDateString(),
            'return_date' => null,
            'adults' => 1,
            'cabin_class' => 'ECONOMY',
            'target_price' => 250,
            'currency' => 'usd',
        ]);

        $alert = FlightAlert::first();

        $response->assertRedirect(route('flight-alerts.show', $alert));
        $this->assertSame($user->id, $alert->user_id);
        $this->assertSame('JFK', $alert->origin);
        $this->assertSame('LAX', $alert->destination);
        $this->assertSame('USD', $alert->currency);
    }

    public function test_a_user_cannot_view_another_users_flight_alert(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $alert = FlightAlert::factory()->for($owner)->create();

        $this->actingAs($intruder)
            ->get(route('flight-alerts.show', $alert))
            ->assertForbidden();
    }

    public function test_checking_an_alert_records_a_price_and_notifies_when_target_is_met(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $alert = FlightAlert::factory()->for($user)->create([
            'origin' => 'JFK',
            'destination' => 'LAX',
            'target_price' => 300,
            'currency' => 'USD',
        ]);

        Http::fake([
            '*/v1/security/oauth2/token' => Http::response(['access_token' => 'fake-token', 'expires_in' => 1799]),
            '*/v2/shopping/flight-offers*' => Http::response([
                'data' => [
                    [
                        'price' => ['grandTotal' => '199.00', 'currency' => 'USD'],
                        'validatingAirlineCodes' => ['AA'],
                    ],
                    [
                        'price' => ['grandTotal' => '249.00', 'currency' => 'USD'],
                        'validatingAirlineCodes' => ['DL'],
                    ],
                ],
            ]),
        ]);

        (new CheckFlightAlertPrice($alert))->handle(app(FlightPriceService::class));

        $alert->refresh();

        $this->assertSame(199.0, (float) $alert->last_price);
        $this->assertNotNull($alert->triggered_at);
        $this->assertSame(1, $alert->priceChecks()->count());

        Notification::assertSentTo($user, FlightPriceAlertNotification::class);
    }
}
