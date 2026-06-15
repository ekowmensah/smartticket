<?php

namespace App\Policies;

use App\Models\User;
use App\Support\PermissionScope;
use function getPermissionsTeamId;
use function setPermissionsTeamId;

class SettingPolicy
{
    public function viewPlatformSettings(User $user): bool
    {
        return $this->canForPlatform($user, 'settings.manage');
    }

    public function updatePlatformSettings(User $user): bool
    {
        return $this->canForPlatform($user, 'settings.manage');
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
