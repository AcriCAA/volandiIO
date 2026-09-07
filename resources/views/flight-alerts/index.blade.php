<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Flight Alerts') }}
            </h2>
            <a href="{{ route('flight-alerts.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('New Alert') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <div class="p-4 bg-green-50 text-green-700 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($alerts->isEmpty())
                    <div class="p-6 text-gray-600">
                        {{ __("You don't have any flight alerts yet.") }}
                        <a href="{{ route('flight-alerts.create') }}" class="text-indigo-600 underline">{{ __('Create your first one') }}</a>.
                    </div>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <th class="px-6 py-3">{{ __('Route') }}</th>
                                <th class="px-6 py-3">{{ __('Dates') }}</th>
                                <th class="px-6 py-3">{{ __('Target Price') }}</th>
                                <th class="px-6 py-3">{{ __('Last Price') }}</th>
                                <th class="px-6 py-3">{{ __('Status') }}</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($alerts as $alert)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $alert->origin }} &rarr; {{ $alert->destination }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $alert->departure_date->toFormattedDateString() }}
                                        @if ($alert->return_date)
                                            &ndash; {{ $alert->return_date->toFormattedDateString() }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ $alert->currency }} {{ $alert->target_price }}</td>
                                    <td class="px-6 py-4 text-gray-600">
                                        @if ($alert->last_price)
                                            {{ $alert->currency }} {{ $alert->last_price }}
                                        @else
                                            <span class="text-gray-400">{{ __('Not checked yet') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($alert->triggered_at)
                                            <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ __('Target met') }}</span>
                                        @elseif (! $alert->is_active)
                                            <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ __('Paused') }}</span>
                                        @else
                                            <span class="inline-flex px-2 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('Watching') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('flight-alerts.show', $alert) }}" class="text-indigo-600 hover:underline">{{ __('View') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
