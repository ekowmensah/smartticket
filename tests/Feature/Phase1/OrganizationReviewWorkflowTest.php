<?php

namespace Tests\Feature\Phase1;

use App\Actions\Organizations\RegisterOrganizationAction;
use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationStatus;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_can_approve_an_organization(): void
    {
        $this->seed(DatabaseSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Naa Dodoo',
            'email' => 'naa@example.com',
            'phone_number' => '+233200000301',
            'organization_name' => 'Naa Tickets',
            'organization_type' => 'business',
            'organization_email' => 'hello@naatickets.test',
            'organization_phone' => '+233200000302',
            'password' => 'password',
        ]);

        $organization = $registration['organization'];
        $platformAdmin = User::query()->where('email', 'platform.admin@smartcast.test')->firstOrFail();

        $response = $this->actingAs($platformAdmin)->patch(route('platform.organizations.review', $organization), [
            'action' => 'approve',
        ]);

        $response->assertRedirect(route('platform.organizations.show', $organization));
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'status' => OrganizationStatus::ACTIVE->value,
            'approval_status' => OrganizationApprovalStatus::APPROVED->value,
            'approved_by' => $platformAdmin->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'organization.approved',
            'organization_id' => $organization->id,
        ]);
    }

    public function test_suspended_organizer_cannot_access_protected_organizer_routes(): void
    {
        $this->seed(DatabaseSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Kojo Asare',
            'email' => 'kojo@example.com',
            'phone_number' => '+233200000401',
            'organization_name' => 'Kojo Arena',
            'organization_type' => 'business',
            'organization_email' => 'team@kojoarena.test',
            'organization_phone' => '+233200000402',
            'password' => 'password',
        ]);

        $organization = $registration['organization'];
        $platformAdmin = User::query()->where('email', 'platform.admin@smartcast.test')->firstOrFail();

        $this->actingAs($platformAdmin)->patch(route('platform.organizations.review', $organization), [
            'action' => 'suspend',
            'reason' => 'Compliance review required',
        ]);

        $response = $this->actingAs($registration['user'])
            ->get(route('organizer.dashboard', $organization));

        $response->assertForbidden();
        $this->assertDatabaseHas('organizations', [
            'id' => $organization->id,
            'status' => OrganizationStatus::SUSPENDED->value,
            'suspension_reason' => 'Compliance review required',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'organization.suspended',
            'organization_id' => $organization->id,
        ]);
    }
}
