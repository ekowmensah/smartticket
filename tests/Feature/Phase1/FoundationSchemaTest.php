<?php

namespace Tests\Feature\Phase1;

use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use function setPermissionsTeamId;

class FoundationSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_phase_one_foundation_tables_exist(): void
    {
        $tables = [
            'organizations',
            'organization_user',
            'organization_invitations',
            'organization_kyc_submissions',
            'organization_documents',
            'otp_requests',
            'customers',
            'settings',
            'audit_logs',
            'roles',
            'permissions',
            'model_has_roles',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(Schema::hasTable($table), "Expected [{$table}] table to exist.");
        }
    }

    public function test_organizer_roles_are_team_scoped_by_organization(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();
        $organizationA = Organization::factory()->create();
        $organizationB = Organization::factory()->create();

        setPermissionsTeamId($organizationA->id);
        $user->assignRole('organizer-owner');

        setPermissionsTeamId($organizationA->id);
        $this->assertTrue($user->hasRole('organizer-owner'));

        $user = $user->fresh();
        setPermissionsTeamId($organizationB->id);
        $this->assertFalse($user->hasRole('organizer-owner'));
    }
}
