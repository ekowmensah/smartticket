<?php

namespace App\Policies;

use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationStatus;
use App\Models\Organization;
use App\Models\User;
use App\Support\PermissionScope;
use function getPermissionsTeamId;
use function setPermissionsTeamId;

class OrganizationPolicy
{
    public function viewDashboard(User $user, Organization $organization): bool
    {
        return $this->hasActiveMembership($user, $organization);
    }

    public function submitKyc(User $user, Organization $organization): bool
    {
        return $this->hasActiveMembership($user, $organization);
    }

    public function viewTeam(User $user, Organization $organization): bool
    {
        return $this->hasActiveMembership($user, $organization);
    }

    public function inviteTeam(User $user, Organization $organization): bool
    {
        return $this->hasActiveMembership($user, $organization)
            && $this->canWithinOrganization($user, $organization, 'team.manage');
    }

    public function review(User $user, Organization $organization): bool
    {
        return $this->canForPlatform($user, 'organizations.review');
    }

    public function reviewKyc(User $user, Organization $organization): bool
    {
        return $this->canForPlatform($user, 'kyc.review');
    }

    private function hasActiveMembership(User $user, Organization $organization): bool
    {
        if ($organization->status === OrganizationStatus::SUSPENDED) {
            return false;
        }

        return $user->memberships()
            ->where('organization_id', $organization->id)
            ->where('status', OrganizationMembershipStatus::ACTIVE->value)
            ->exists();
    }

    private function canWithinOrganization(User $user, Organization $organization, string $permission): bool
    {
        $originalTeamId = getPermissionsTeamId();
        setPermissionsTeamId($organization->id);

        try {
            return $user->can($permission);
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }

    private function canForPlatform(User $user, string $permission): bool
    {
        $originalTeamId = getPermissionsTeamId();
        setPermissionsTeamId(PermissionScope::PLATFORM);

        try {
            return $user->can($permission);
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }
}
