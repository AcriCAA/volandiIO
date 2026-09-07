@php
    $alert = $alert ?? null;
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <div>
        <x-input-label for="origin" :value="__('Origin airport code')" />
        <x-text-input id="origin" name="origin" type="text" maxlength="3" placeholder="JFK" class="mt-1 block w-full uppercase" :value="old('origin', $alert?->origin)" required autofocus />
        <x-input-error :messages="$errors->get('origin')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="destination" :value="__('Destination airport code')" />
        <x-text-input id="destination" name="destination" type="text" maxlength="3" placeholder="LAX" class="mt-1 block w-full uppercase" :value="old('destination', $alert?->destination)" required />
        <x-input-error :messages="$errors->get('destination')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="departure_date" :value="__('Departure date')" />
        <x-text-input id="departure_date" name="departure_date" type="date" class="mt-1 block w-full" :value="old('departure_date', $alert?->departure_date?->toDateString())" required />
        <x-input-error :messages="$errors->get('departure_date')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="return_date" :value="__('Return date (optional)')" />
        <x-text-input id="return_date" name="return_date" type="date" class="mt-1 block w-full" :value="old('return_date', $alert?->return_date?->toDateString())" />
        <x-input-error :messages="$errors->get('return_date')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="adults" :value="__('Adults')" />
        <x-text-input id="adults" name="adults" type="number" min="1" max="9" class="mt-1 block w-full" :value="old('adults', $alert?->adults ?? 1)" required />
        <x-input-error :messages="$errors->get('adults')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="cabin_class" :value="__('Cabin class')" />
        <select id="cabin_class" name="cabin_class" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
            @foreach (['ECONOMY' => 'Economy', 'PREMIUM_ECONOMY' => 'Premium Economy', 'BUSINESS' => 'Business', 'FIRST' => 'First'] as $value => $label)
                <option value="{{ $value }}" @selected(old('cabin_class', $alert?->cabin_class ?? 'ECONOMY') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('cabin_class')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="target_price" :value="__('Target price')" />
        <x-text-input id="target_price" name="target_price" type="number" step="0.01" min="1" class="mt-1 block w-full" :value="old('target_price', $alert?->target_price)" required />
        <x-input-error :messages="$errors->get('target_price')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="currency" :value="__('Currency')" />
        <x-text-input id="currency" name="currency" type="text" maxlength="3" placeholder="USD" class="mt-1 block w-full uppercase" :value="old('currency', $alert?->currency ?? 'USD')" required />
        <x-input-error :messages="$errors->get('currency')" class="mt-2" />
    </div>

    @if ($alert)
        <div class="flex items-center">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('is_active', $alert->is_active))>
                <span class="ms-2 text-sm text-gray-600">{{ __('Actively monitor this alert') }}</span>
            </label>
        </div>
    @endif
</div>
