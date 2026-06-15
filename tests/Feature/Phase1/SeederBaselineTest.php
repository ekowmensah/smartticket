<?php

namespace Tests\Feature\Phase1;

use App\Support\PermissionScope;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use function setPermissionsTeamId;

class SeederBaselineTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_platform_admin_with_super_admin_role(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'platform.admin@smartcast.test',
        ]);

        setPermissionsTeamId(PermissionScope::PLATFORM);

        $this->assertNotNull(Role::findByName('super-admin', 'web'));
        $this->assertDatabaseHas('model_has_roles', [
            'organization_id' => PermissionScope::PLATFORM,
        ]);
    }
}
