<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFlightAlertRequest;
use App\Models\FlightAlert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FlightAlertController extends Controller
{
    public function index(Request $request): View
    {
        $alerts = $request->user()->flightAlerts()->latest()->get();

        return view('flight-alerts.index', compact('alerts'));
    }

    public function create(): View
    {
        return view('flight-alerts.create');
    }

    public function store(StoreFlightAlertRequest $request): RedirectResponse
    {
        $alert = $request->user()->flightAlerts()->create($request->validated());

        return redirect()
            ->route('flight-alerts.show', $alert)
            ->with('status', 'Flight alert created. We will start checking prices on the next scheduled run.');
    }

    public function show(FlightAlert $flightAlert): View
    {
        $this->authorizeAlert($flightAlert);

        $flightAlert->load(['priceChecks' => fn ($query) => $query->latest()->limit(20)]);

        return view('flight-alerts.show', ['alert' => $flightAlert]);
    }

    public function edit(FlightAlert $flightAlert): View
    {
        $this->authorizeAlert($flightAlert);

        return view('flight-alerts.edit', ['alert' => $flightAlert]);
    }

    public function update(StoreFlightAlertRequest $request, FlightAlert $flightAlert): RedirectResponse
    {
        $this->authorizeAlert($flightAlert);

        $flightAlert->update([
            ...$request->validated(),
            'triggered_at' => null,
        ]);

        return redirect()
            ->route('flight-alerts.show', $flightAlert)
            ->with('status', 'Flight alert updated.');
    }

    public function destroy(FlightAlert $flightAlert): RedirectResponse
    {
        $this->authorizeAlert($flightAlert);

        $flightAlert->delete();

        return redirect()
            ->route('flight-alerts.index')
            ->with('status', 'Flight alert deleted.');
    }

    private function authorizeAlert(FlightAlert $flightAlert): void
    {
        abort_unless($flightAlert->user_id === auth()->id(), 403);
    }
}
