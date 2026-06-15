<?php

namespace App\Actions\Organizations;

use App\Models\Organization;
use App\Models\OrganizationInvitation;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateOrganizationInvitationAction
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{invitation: OrganizationInvitation, token: string}
     */
    public function execute(Organization $organization, User $actor, array $data): array
    {
        return DB::transaction(function () use ($organization, $actor, $data): array {
            $token = Str::random(64);

            $invitation = OrganizationInvitation::create([
                'organization_id' => $organization->id,
                'invited_by' => $actor->id,
                'name' => $data['name'] ?? null,
                'email' => strtolower((string) $data['email']),
                'role' => $data['role'],
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addDays(7),
            ]);

            $this->auditLogger->log(
                actor: $actor,
                description: 'Organization invitation created',
                event: 'organization_invitation.created',
                subject: $invitation,
                organizationId: $organization->id,
                properties: [
                    'email' => $invitation->email,
                    'role' => $invitation->role,
                ],
            );

            return [
                'invitation' => $invitation,
                'token' => $token,
            ];
        });
    }
}
