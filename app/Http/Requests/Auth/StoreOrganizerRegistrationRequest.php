<?php

namespace App\Http\Requests\Auth;

use App\Enums\OrganizationType;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreOrganizerRegistrationRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_number' => ['required', 'string', 'max:32', 'regex:/^\+?[0-9]{10,15}$/', 'unique:'.User::class.',phone_number'],
            'organization_name' => ['required', 'string', 'max:255'],
            'organization_type' => ['required', Rule::in(array_column(OrganizationType::cases(), 'value'))],
            'organization_email' => ['nullable', 'string', 'lowercase', 'email', 'max:255'],
            'organization_phone' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9]{10,15}$/'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower((string) $this->input('email')),
            'organization_email' => $this->filled('organization_email')
                ? strtolower((string) $this->input('organization_email'))
                : null,
            'phone_number' => $this->normalizePhone((string) $this->input('phone_number')),
            'organization_phone' => $this->filled('organization_phone')
                ? $this->normalizePhone((string) $this->input('organization_phone'))
                : null,
        ]);
    }

    private function normalizePhone(string $value): string
    {
        return preg_replace('/[^0-9+]/', '', $value) ?? $value;
    }
}
