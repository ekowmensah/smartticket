<?php

namespace App\Support;

final class OrganizationRoleCatalog
{
    /**
     * @return array<string, string>
     */
    public static function assignableRoles(): array
    {
        return [
            'organizer-admin' => 'Organizer Admin',
            'organizer-finance' => 'Organizer Finance',
            'organizer-support' => 'Organizer Support',
            'organizer-scanner-manager' => 'Scanner Manager',
            'organizer-member' => 'Team Member',
        ];
    }

    private function __construct()
    {
    }
}
