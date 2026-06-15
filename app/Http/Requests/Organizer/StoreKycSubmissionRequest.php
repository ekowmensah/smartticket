<?php

namespace App\Http\Requests\Organizer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKycSubmissionRequest extends FormRequest
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
            'business_type' => ['required', 'string', 'max:255'],
            'registration_number' => ['nullable', 'string', 'max:255'],
            'tax_identifier' => ['nullable', 'string', 'max:255'],
            'legal_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:32', 'regex:/^\+?[0-9]{10,15}$/'],
            'contact_email' => ['required', 'string', 'email', 'max:255'],
            'payout_method' => ['required', Rule::in(['bank_transfer', 'mobile_money'])],
            'payout_account_name' => ['required', 'string', 'max:255'],
            'payout_account_number' => ['required', 'string', 'max:255'],
            'payout_provider' => ['nullable', 'string', 'max:255'],
            'documents' => ['required', 'array', 'min:1'],
            'documents.*.type' => ['required', Rule::in([
                'registration_certificate',
                'government_id',
                'bank_or_momo_proof',
            ])],
            'documents.*.file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'contact_email' => strtolower((string) $this->input('contact_email')),
            'contact_phone' => preg_replace('/[^0-9+]/', '', (string) $this->input('contact_phone')),
        ]);
    }
}
