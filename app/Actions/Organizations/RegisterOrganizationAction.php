<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationStatus;
use App\Enums\UserStatus;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;
use function setPermissionsTeamId;

class RegisterOrganizationAction
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array{user: User, organization: Organization}
     */
    public function execute(array $data): array
    {
        return DB::transaction(function () use ($data): array {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'status' => UserStatus::ACTIVE,
                'password' => Hash::make($data['password']),
            ]);

            $organization = Organization::create([
                'public_id' => (string) Str::ulid(),
                'name' => $data['organization_name'],
                'slug' => $this->makeUniqueSlug($data['organization_name']),
                'type' => $data['organization_type'],
                'public_email' => $data['organization_email'] ?? $data['email'],
                'public_phone' => $data['organization_phone'] ?? $data['phone_number'],
                'status' => OrganizationStatus::PENDING,
                'approval_status' => OrganizationApprovalStatus::PENDING,
                'timezone' => 'Africa/Accra',
                'currency_code' => 'GHS',
                'country_code' => 'GH',
                'created_by' => $user->id,
            ]);

            $organization->memberships()->create([
                'user_id' => $user->id,
                'role' => 'organizer-owner',
                'status' => OrganizationMembershipStatus::ACTIVE,
                'is_owner' => true,
                'invited_by' => $user->id,
                'invited_at' => now(),
                'joined_at' => now(),
            ]);

            app(PermissionRegistrar::class)->forgetCachedPermissions();

            setPermissionsTeamId(null);
            $role = Role::findOrCreate('organizer-owner', 'web');

            setPermissionsTeamId($organization->id);
            $user->assignRole($role);
            setPermissionsTeamId(null);

            $this->auditLogger->log(
                actor: $user,
                description: 'Organization registered',
                event: 'organization.registered',
                subject: $organization,
                organizationId: $organization->id,
                properties: [
                    'organization_name' => $organization->name,
                    'owner_user_id' => $user->id,
                ],
            );

            return [
                'user' => $user,
                'organization' => $organization,
            ];
        });
    }

    private function makeUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Organization::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
