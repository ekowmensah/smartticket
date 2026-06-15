<?php

namespace App\Http\Requests\Platform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlatformSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'product_name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'string', 'email', 'max:255'],
            'support_phone' => ['required', 'string', 'max:32', 'regex:/^\+?[0-9]{10,15}$/'],
            'currency_code' => ['required', 'string', 'size:3'],
            'timezone' => ['required', 'timezone'],
            'date_format' => ['required', Rule::in(['d M Y', 'd/m/Y', 'Y-m-d'])],
            'contact_address' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'support_email' => strtolower((string) $this->input('support_email')),
            'support_phone' => preg_replace('/[^0-9+]/', '', (string) $this->input('support_phone')),
            'currency_code' => strtoupper((string) $this->input('currency_code')),
        ]);
    }
}
