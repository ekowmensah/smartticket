<?php

namespace Tests\Feature\Phase1;

use App\Actions\Organizations\RegisterOrganizationAction;
use App\Enums\OrganizationMembershipStatus;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use function setPermissionsTeamId;

class OrganizationTeamInvitationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_owner_can_create_team_invitation(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Adjoa Manu',
            'email' => 'adjoa@example.com',
            'phone_number' => '+233200000501',
            'organization_name' => 'Adjoa Events',
            'organization_type' => 'business',
            'organization_email' => 'team@adjoaevents.test',
            'organization_phone' => '+233200000502',
            'password' => 'password',
        ]);

        $organization = $registration['organization'];

        $response = $this->actingAs($registration['user'])
            ->post(route('organizer.team.store', $organization), [
                'name' => 'Kwesi Worker',
                'email' => 'kwesi.worker@example.com',
                'role' => 'organizer-member',
            ]);

        $response->assertRedirect(route('organizer.team.index', $organization));
        $this->assertDatabaseHas('organization_invitations', [
            'organization_id' => $organization->id,
            'email' => 'kwesi.worker@example.com',
            'role' => 'organizer-member',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'organization_invitation.created',
            'organization_id' => $organization->id,
        ]);
    }

    public function test_guest_can_accept_invitation_and_join_team(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Esi Lamptey',
            'email' => 'esi@example.com',
            'phone_number' => '+233200000601',
            'organization_name' => 'Esi Live',
            'organization_type' => 'business',
            'organization_email' => 'team@esilive.test',
            'organization_phone' => '+233200000602',
            'password' => 'password',
        ]);

        $owner = $registration['user'];
        $organization = $registration['organization'];

        $inviteResponse = $this->actingAs($owner)->post(route('organizer.team.store', $organization), [
            'name' => 'Team Mate',
            'email' => 'teammate@example.com',
            'role' => 'organizer-support',
        ]);
        $inviteResponse->assertSessionHas('invitation_link');

        $invitation = \App\Models\OrganizationInvitation::query()
            ->where('organization_id', $organization->id)
            ->where('email', 'teammate@example.com')
            ->firstOrFail();

        $invitationLink = session('invitation_link');
        $token = basename((string) parse_url($invitationLink, PHP_URL_PATH));

        Auth::logout();

        $response = $this->post(route('invitations.accept', ['token' => $token]), [
            'name' => 'Team Mate',
            'phone_number' => '+233200000603',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('organizer.dashboard', $organization));
        $this->assertDatabaseHas('organization_user', [
            'organization_id' => $organization->id,
            'role' => 'organizer-support',
            'status' => OrganizationMembershipStatus::ACTIVE->value,
        ]);
        $this->assertDatabaseHas('organization_invitations', [
            'id' => $invitation->id,
            'accepted_by' => \App\Models\User::query()->where('email', 'teammate@example.com')->value('id'),
        ]);

        $invitedUser = \App\Models\User::query()->where('email', 'teammate@example.com')->firstOrFail();
        setPermissionsTeamId($organization->id);
        $this->assertTrue($invitedUser->fresh()->hasRole('organizer-support'));
    }

    public function test_organizer_without_team_manage_permission_cannot_invite_members(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Nii Kpakpo',
            'email' => 'nii@example.com',
            'phone_number' => '+233200000701',
            'organization_name' => 'Nii Arena',
            'organization_type' => 'business',
            'organization_email' => 'team@niiarena.test',
            'organization_phone' => '+233200000702',
            'password' => 'password',
        ]);

        $organization = $registration['organization'];

        $memberRegistration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Ordinary Member',
            'email' => 'member@example.com',
            'phone_number' => '+233200000703',
            'organization_name' => 'Member Sandbox',
            'organization_type' => 'business',
            'organization_email' => 'sandbox@example.com',
            'organization_phone' => '+233200000704',
            'password' => 'password',
        ]);

        $member = $memberRegistration['user'];

        $organization->memberships()->create([
            'user_id' => $member->id,
            'role' => 'organizer-member',
            'status' => OrganizationMembershipStatus::ACTIVE,
            'is_owner' => false,
            'invited_by' => $registration['user']->id,
            'invited_at' => now(),
            'joined_at' => now(),
        ]);

        setPermissionsTeamId($organization->id);
        $member->assignRole('organizer-member');
        setPermissionsTeamId(null);

        $response = $this->actingAs($member)
            ->post(route('organizer.team.store', $organization), [
                'name' => 'Blocked Invite',
                'email' => 'blocked@example.com',
                'role' => 'organizer-member',
            ]);

        $response->assertForbidden();
    }
}
