<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $alert->origin }} &rarr; {{ $alert->destination }}
            </h2>
            <a href="{{ route('flight-alerts.edit', $alert) }}" class="text-sm text-indigo-600 hover:underline">{{ __('Edit') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Departure') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $alert->departure_date->toFormattedDateString() }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Return') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $alert->return_date?->toFormattedDateString() ?? __('One-way') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Passengers') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $alert->adults }} adult(s), {{ ucfirst(strtolower(str_replace('_', ' ', $alert->cabin_class))) }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Target price') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $alert->currency }} {{ $alert->target_price }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Last checked') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $alert->last_checked_at?->diffForHumans() ?? __('Not checked yet') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Status') }}</dt>
                        <dd class="font-medium text-gray-900">
                            @if ($alert->triggered_at)
                                {{ __('Target met on :date', ['date' => $alert->triggered_at->toFormattedDateString()]) }}
                            @elseif (! $alert->is_active)
                                {{ __('Paused') }}
                            @else
                                {{ __('Watching for price drops') }}
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">{{ __('Price history') }}</h3>
                </div>
                @if ($alert->priceChecks->isEmpty())
                    <div class="p-6 text-gray-500 text-sm">
                        {{ __('No price checks recorded yet. This runs automatically on a schedule.') }}
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">{{ __('Checked') }}</th>
                                <th class="px-6 py-3">{{ __('Price') }}</th>
                                <th class="px-6 py-3">{{ __('Carrier') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($alert->priceChecks as $check)
                                <tr>
                                    <td class="px-6 py-4 text-gray-600">{{ $check->created_at->diffForHumans() }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $check->currency }} {{ $check->price }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $check->carrier_code ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
