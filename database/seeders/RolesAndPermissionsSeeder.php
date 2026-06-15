<?php

namespace Database\Seeders;

use App\Support\PermissionScope;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use function setPermissionsTeamId;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'platform.access',
            'organizations.review',
            'organizations.suspend',
            'settings.manage',
            'audit.view',
            'users.manage',
            'roles.manage',
            'organizer.access',
            'organization.manage',
            'team.manage',
            'kyc.submit',
            'kyc.review',
            'dashboard.view',
        ];

        $permissionModels = collect($permissions)
            ->mapWithKeys(fn (string $permission): array => [
                $permission => Permission::findOrCreate($permission, 'web'),
            ]);

        setPermissionsTeamId(null);

        $rolePermissions = [
            'super-admin' => $permissions,
            'platform-admin' => [
                'platform.access',
                'organizations.review',
                'organizations.suspend',
                'settings.manage',
                'audit.view',
                'users.manage',
                'kyc.review',
            ],
            'compliance-officer' => [
                'platform.access',
                'organizations.review',
                'kyc.review',
                'audit.view',
            ],
            'support-administrator' => [
                'platform.access',
                'users.manage',
                'audit.view',
            ],
            'technical-administrator' => [
                'platform.access',
                'settings.manage',
                'roles.manage',
                'audit.view',
            ],
            'organizer-owner' => [
                'organizer.access',
                'organization.manage',
                'team.manage',
                'kyc.submit',
                'dashboard.view',
            ],
            'organizer-admin' => [
                'organizer.access',
                'organization.manage',
                'team.manage',
                'kyc.submit',
                'dashboard.view',
            ],
            'organizer-finance' => [
                'organizer.access',
                'dashboard.view',
            ],
            'organizer-support' => [
                'organizer.access',
                'dashboard.view',
            ],
            'organizer-scanner-manager' => [
                'organizer.access',
                'dashboard.view',
            ],
            'organizer-member' => [
                'organizer.access',
                'dashboard.view',
            ],
        ];

        foreach ($rolePermissions as $roleName => $rolePermissionSet) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($permissionModels->only($rolePermissionSet)->values());
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
