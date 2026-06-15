<?php

namespace Tests\Feature\Phase1;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformDashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_can_access_platform_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $platformAdmin = User::query()->where('email', 'platform.admin@smartcast.test')->firstOrFail();

        $response = $this->actingAs($platformAdmin)->get(route('platform.dashboard'));

        $response->assertOk();
        $response->assertSee('Platform Console');
    }

    public function test_non_platform_user_cannot_access_platform_dashboard(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('platform.dashboard'));

        $response->assertForbidden();
    }
}
