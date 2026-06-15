<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationMembershipStatus;
use App\Enums\UserStatus;
use App\Models\OrganizationInvitation;
use App\Models\Role;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;
use function setPermissionsTeamId;

class AcceptOrganizationInvitationAction
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(OrganizationInvitation $invitation, ?User $authenticatedUser, array $data = []): User
    {
        return DB::transaction(function () use ($invitation, $authenticatedUser, $data): User {
            $user = $authenticatedUser ?? $this->resolveInvitedUser($invitation, $data);

            $membership = $invitation->organization->memberships()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'role' => $invitation->role,
                    'status' => OrganizationMembershipStatus::ACTIVE,
                    'is_owner' => false,
                    'invited_by' => $invitation->invited_by,
                    'invited_at' => $invitation->created_at,
                    'joined_at' => now(),
                ],
            );

            app(PermissionRegistrar::class)->forgetCachedPermissions();
            setPermissionsTeamId($invitation->organization_id);
            $user->syncRoles([$invitation->role]);
            setPermissionsTeamId(null);

            $invitation->forceFill([
                'accepted_at' => now(),
                'accepted_by' => $user->id,
            ])->save();

            $this->auditLogger->log(
                actor: $user,
                description: 'Organization invitation accepted',
                event: 'organization_invitation.accepted',
                subject: $membership,
                organizationId: $invitation->organization_id,
                properties: [
                    'invitation_id' => $invitation->id,
                    'role' => $invitation->role,
                    'email' => $invitation->email,
                ],
            );

            return $user->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveInvitedUser(OrganizationInvitation $invitation, array $data): User
    {
        $existingUser = User::query()->where('email', $invitation->email)->first();

        if ($existingUser !== null) {
            return $existingUser;
        }

        return User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'phone_number' => $data['phone_number'],
            'status' => UserStatus::ACTIVE,
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);
    }
}
