<?php

namespace Database\Factories;

use App\Models\FlightAlert;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FlightAlert>
 */
class FlightAlertFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'origin' => $this->faker->randomElement(['JFK', 'LAX', 'ORD', 'SFO']),
            'destination' => $this->faker->randomElement(['LHR', 'CDG', 'NRT', 'MIA']),
            'departure_date' => now()->addMonth()->toDateString(),
            'return_date' => null,
            'adults' => 1,
            'cabin_class' => 'ECONOMY',
            'target_price' => $this->faker->randomFloat(2, 100, 800),
            'currency' => 'USD',
            'last_price' => null,
            'last_checked_at' => null,
            'triggered_at' => null,
            'is_active' => true,
        ];
    }
}
