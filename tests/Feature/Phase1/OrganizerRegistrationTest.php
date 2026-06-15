<?php

namespace Tests\Feature\Phase1;

use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use function setPermissionsTeamId;

class OrganizerRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_registration_creates_user_organization_membership_and_role(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $response = $this->post(route('register'), [
            'name' => 'Ama Mensah',
            'email' => 'ama@example.com',
            'phone_number' => '+233201112223',
            'organization_name' => 'Ama Events',
            'organization_type' => 'business',
            'organization_email' => 'hello@amaevents.test',
            'organization_phone' => '+233209998887',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::query()->where('email', 'ama@example.com')->firstOrFail();
        $organization = Organization::query()->where('name', 'Ama Events')->firstOrFail();

        $response->assertRedirect(route('organizer.dashboard', $organization));
        $this->assertAuthenticatedAs($user);

        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'role' => 'organizer-owner',
            'is_owner' => true,
        ]);

        setPermissionsTeamId($organization->id);
        $this->assertTrue($user->fresh()->hasRole('organizer-owner'));
    }
}
