<?php

namespace Tests\Feature\Phase1;

use App\Actions\Organizations\RegisterOrganizationAction;
use App\Enums\OrganizationKycStatus;
use App\Models\Organization;
use App\Models\OrganizationKycSubmission;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KycWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_organizer_can_submit_kyc_with_documents(): void
    {
        Storage::fake('local');
        $this->seed(RolesAndPermissionsSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Abena Ofori',
            'email' => 'abena@example.com',
            'phone_number' => '+233200000101',
            'organization_name' => 'Abena Events',
            'organization_type' => 'business',
            'organization_email' => 'hello@abenaevents.test',
            'organization_phone' => '+233200000102',
            'password' => 'password',
        ]);

        $organization = $registration['organization'];

        $response = $this->actingAs($registration['user'])->put(route('organizer.kyc.update', $organization), [
            'business_type' => 'Limited liability company',
            'registration_number' => 'REG-12345',
            'tax_identifier' => 'TIN-00077',
            'legal_name' => 'Abena Events Limited',
            'contact_name' => 'Abena Ofori',
            'contact_phone' => '+233200000103',
            'contact_email' => 'compliance@abenaevents.test',
            'payout_method' => 'mobile_money',
            'payout_account_name' => 'Abena Events',
            'payout_account_number' => '233200000104',
            'payout_provider' => 'MTN',
            'documents' => [
                [
                    'type' => 'registration_certificate',
                    'file' => UploadedFile::fake()->create('registration.pdf', 120, 'application/pdf'),
                ],
                [
                    'type' => 'government_id',
                    'file' => UploadedFile::fake()->image('government-id.jpg'),
                ],
                [
                    'type' => 'bank_or_momo_proof',
                    'file' => UploadedFile::fake()->create('momo-proof.pdf', 80, 'application/pdf'),
                ],
            ],
        ]);

        $response->assertRedirect(route('organizer.kyc.edit', $organization));

        $submission = OrganizationKycSubmission::query()
            ->where('organization_id', $organization->id)
            ->firstOrFail();

        $this->assertSame(OrganizationKycStatus::SUBMITTED, $submission->status);
        $this->assertDatabaseCount('organization_documents', 3);
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'organization_kyc.submitted',
            'organization_id' => $organization->id,
        ]);

        foreach ($submission->documents as $document) {
            Storage::disk('local')->assertExists($document->storage_path);
        }
    }

    public function test_platform_admin_can_approve_kyc_submission(): void
    {
        Storage::fake('local');
        $this->seed(DatabaseSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Yaw Bediako',
            'email' => 'yaw@example.com',
            'phone_number' => '+233200000201',
            'organization_name' => 'Yaw Live',
            'organization_type' => 'business',
            'organization_email' => 'ops@yawlive.test',
            'organization_phone' => '+233200000202',
            'password' => 'password',
        ]);

        $organization = $registration['organization'];
        $platformAdmin = User::query()->where('email', 'platform.admin@smartcast.test')->firstOrFail();

        $this->actingAs($registration['user'])->put(route('organizer.kyc.update', $organization), [
            'business_type' => 'Sole proprietor',
            'registration_number' => 'REG-882',
            'tax_identifier' => 'TIN-552',
            'legal_name' => 'Yaw Live',
            'contact_name' => 'Yaw Bediako',
            'contact_phone' => '+233200000203',
            'contact_email' => 'team@yawlive.test',
            'payout_method' => 'bank_transfer',
            'payout_account_name' => 'Yaw Live',
            'payout_account_number' => '0011223344',
            'payout_provider' => 'GCB',
            'documents' => [
                [
                    'type' => 'registration_certificate',
                    'file' => UploadedFile::fake()->create('registration.pdf', 50, 'application/pdf'),
                ],
                [
                    'type' => 'government_id',
                    'file' => UploadedFile::fake()->image('government-id.png'),
                ],
                [
                    'type' => 'bank_or_momo_proof',
                    'file' => UploadedFile::fake()->create('bank-proof.pdf', 50, 'application/pdf'),
                ],
            ],
        ]);

        $response = $this->actingAs($platformAdmin)->patch(route('platform.organizations.kyc.review', $organization), [
            'action' => 'approve',
        ]);

        $response->assertRedirect(route('platform.organizations.show', $organization));
        $this->assertDatabaseHas('organization_kyc_submissions', [
            'organization_id' => $organization->id,
            'status' => OrganizationKycStatus::APPROVED->value,
            'reviewed_by' => $platformAdmin->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'organization_kyc.approved',
            'organization_id' => $organization->id,
        ]);
    }
}
