<?php

namespace App\Http\Requests\Public;

use App\Models\OrganizationInvitation;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class AcceptOrganizationInvitationRequest extends FormRequest
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
        $invitation = OrganizationInvitation::fromToken((string) $this->route('token'));
        $existingUser = User::query()->where('email', $invitation->email)->exists();

        if ($this->user() !== null) {
            return [];
        }

        if ($existingUser) {
            return [];
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:32', 'regex:/^\+?[0-9]{10,15}$/', 'unique:users,phone_number'],
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('phone_number')) {
            $this->merge([
                'phone_number' => preg_replace('/[^0-9+]/', '', (string) $this->input('phone_number')),
            ]);
        }
    }

    protected function passedValidation(): void
    {
        $invitation = OrganizationInvitation::fromToken((string) $this->route('token'));

        if ($this->user() !== null) {
            abort_unless($this->user()->email === $invitation->email, 403);
            return;
        }

        $existingUser = User::query()->where('email', $invitation->email)->first();

        if ($existingUser !== null) {
            abort(403, 'Please sign in with the invited email address before accepting this invitation.');
        }
    }
}
