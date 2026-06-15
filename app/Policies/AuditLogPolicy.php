<?php

namespace App\Policies;

use App\Models\User;
use App\Support\PermissionScope;
use function getPermissionsTeamId;
use function setPermissionsTeamId;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        $originalTeamId = getPermissionsTeamId();
        setPermissionsTeamId(PermissionScope::PLATFORM);

        try {
            return $user->can('audit.view');
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }
}
