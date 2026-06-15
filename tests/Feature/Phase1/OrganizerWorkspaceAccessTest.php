<?php

namespace Tests\Feature\Phase1;

use App\Actions\Organizations\RegisterOrganizationAction;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function setPermissionsTeamId;

class OrganizerWorkspaceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_access_their_own_workspace(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Kofi Boateng',
            'email' => 'kofi@example.com',
            'phone_number' => '+233208887776',
            'organization_name' => 'Kofi Live',
            'organization_type' => 'business',
            'organization_email' => 'team@kofilive.test',
            'organization_phone' => '+233205556667',
            'password' => 'password',
        ]);

        $response = $this->actingAs($registration['user'])
            ->get(route('organizer.dashboard', $registration['organization']));

        $response->assertOk();
        $response->assertSee('Organizer Workspace');
        $response->assertSee('Kofi Live');
    }

    public function test_organizer_cannot_access_another_organization_workspace(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Efua Owusu',
            'email' => 'efua@example.com',
            'phone_number' => '+233207770001',
            'organization_name' => 'Efua Concerts',
            'organization_type' => 'business',
            'organization_email' => 'team@efuaconcerts.test',
            'organization_phone' => '+233202220001',
            'password' => 'password',
        ]);

        $otherOrganization = Organization::factory()->create();

        $response = $this->actingAs($registration['user'])
            ->get(route('organizer.dashboard', $otherOrganization));

        $response->assertForbidden();
    }
}
