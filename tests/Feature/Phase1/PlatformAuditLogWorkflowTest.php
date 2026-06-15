<?php

namespace Tests\Feature\Phase1;

use App\Actions\Organizations\RegisterOrganizationAction;
use App\Models\AuditLog;
use App\Support\AuditLogger;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformAuditLogWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_can_view_audit_logs(): void
    {
        $this->seed(DatabaseSeeder::class);

        app(RegisterOrganizationAction::class)->execute([
            'name' => 'Audit Owner',
            'email' => 'audit-owner@example.com',
            'phone_number' => '+233200001301',
            'organization_name' => 'Audit Org',
            'organization_type' => 'business',
            'organization_email' => 'audit@org.test',
            'organization_phone' => '+233200001302',
            'password' => 'password',
        ]);

        $platformAdmin = \App\Models\User::query()->where('email', 'platform.admin@smartcast.test')->firstOrFail();

        $response = $this->actingAs($platformAdmin)->get(route('platform.audit-logs.index'));

        $response->assertOk();
        $response->assertSee('Audit Logs');
        $response->assertSee('Organization registered');
    }

    public function test_non_platform_user_cannot_view_audit_logs(): void
    {
        $this->seed(DatabaseSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Blocked Viewer',
            'email' => 'blocked-viewer@example.com',
            'phone_number' => '+233200001311',
            'organization_name' => 'Blocked Viewer Org',
            'organization_type' => 'business',
            'organization_email' => 'blocked-viewer@org.test',
            'organization_phone' => '+233200001312',
            'password' => 'password',
        ]);

        $response = $this->actingAs($registration['user'])->get(route('platform.audit-logs.index'));

        $response->assertForbidden();
    }

    public function test_audit_logger_masks_sensitive_properties(): void
    {
        $this->seed(DatabaseSeeder::class);

        request()->headers->set('X-Request-ID', 'audit-mask-request');

        app(AuditLogger::class)->log(
            actor: null,
            description: 'Sensitive payload captured',
            event: 'audit.masked',
            properties: [
                'password' => 'super-secret',
                'invitation_token' => 'accept-me',
                'nested' => [
                    'remember_token' => 'remember-me',
                    'currency_code' => 'GHS',
                ],
            ],
        );

        $auditLog = AuditLog::query()->where('event', 'audit.masked')->firstOrFail();
        $properties = $auditLog->properties->toArray();

        $this->assertSame('[REDACTED]', $properties['password']);
        $this->assertSame('[REDACTED]', $properties['invitation_token']);
        $this->assertSame('[REDACTED]', $properties['nested']['remember_token']);
        $this->assertSame('GHS', $properties['nested']['currency_code']);
    }
}
