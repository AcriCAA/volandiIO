<?php

namespace App\Console\Commands;

use App\Jobs\CheckFlightAlertPrice;
use App\Models\FlightAlert;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('flights:check-alerts')]
#[Description('Dispatch a price check for every active, upcoming flight alert')]
class CheckFlightAlerts extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $alerts = FlightAlert::query()
            ->where('is_active', true)
            ->whereNull('triggered_at')
            ->whereDate('departure_date', '>=', now()->toDateString())
            ->get();

        foreach ($alerts as $alert) {
            CheckFlightAlertPrice::dispatch($alert);
        }

        $this->info("Dispatched price checks for {$alerts->count()} active flight alert(s).");
    }
}
