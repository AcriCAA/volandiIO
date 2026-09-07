<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFlightAlertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'origin' => ['required', 'string', 'size:3', 'alpha'],
            'destination' => ['required', 'string', 'size:3', 'alpha', 'different:origin'],
            'departure_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['nullable', 'date', 'after:departure_date'],
            'adults' => ['required', 'integer', 'min:1', 'max:9'],
            'cabin_class' => ['required', 'in:ECONOMY,PREMIUM_ECONOMY,BUSINESS,FIRST'],
            'target_price' => ['required', 'numeric', 'min:1'],
            'currency' => ['required', 'string', 'size:3', 'alpha'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'origin' => strtoupper((string) $this->origin),
            'destination' => strtoupper((string) $this->destination),
            'currency' => strtoupper((string) $this->currency),
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
