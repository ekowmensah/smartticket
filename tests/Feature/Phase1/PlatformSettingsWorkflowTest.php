<?php

namespace Tests\Feature\Phase1;

use App\Actions\Organizations\RegisterOrganizationAction;
use App\Models\AuditLog;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformSettingsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_platform_admin_can_update_platform_settings(): void
    {
        $this->seed(DatabaseSeeder::class);

        $platformAdmin = \App\Models\User::query()->where('email', 'platform.admin@smartcast.test')->firstOrFail();

        $response = $this->withHeader('X-Request-ID', 'phase1-settings-request')
            ->withServerVariables([
                'REMOTE_ADDR' => '127.0.0.44',
                'HTTP_USER_AGENT' => 'Phase1TestAgent/1.0',
            ])
            ->actingAs($platformAdmin)
            ->put(route('platform.settings.update'), [
                'product_name' => 'SmartCast Live',
                'support_email' => 'help@smartcast.live',
                'support_phone' => '+233200001111',
                'currency_code' => 'GHS',
                'timezone' => 'Africa/Accra',
                'date_format' => 'd/m/Y',
                'contact_address' => 'Osu, Accra',
            ]);

        $response->assertRedirect(route('platform.settings.edit'));
        $this->assertDatabaseHas('settings', [
            'scope' => 'platform',
            'scope_id' => 0,
            'key' => 'product_name',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'event' => 'platform_settings.updated',
            'causer_id' => $platformAdmin->id,
            'request_id' => 'phase1-settings-request',
            'ip_address' => '127.0.0.44',
        ]);

        $home = $this->get(route('home'));
        $home->assertSee('SmartCast Live');
        $home->assertSee('help@smartcast.live');

        $auditLog = AuditLog::query()->where('event', 'platform_settings.updated')->firstOrFail();

        $this->assertSame('Phase1TestAgent/1.0', $auditLog->user_agent);
    }

    public function test_non_platform_user_cannot_manage_platform_settings(): void
    {
        $this->seed(DatabaseSeeder::class);

        $registration = app(RegisterOrganizationAction::class)->execute([
            'name' => 'Settings Blocked',
            'email' => 'settings-blocked@example.com',
            'phone_number' => '+233200001211',
            'organization_name' => 'Blocked Org',
            'organization_type' => 'business',
            'organization_email' => 'blocked@org.test',
            'organization_phone' => '+233200001212',
            'password' => 'password',
        ]);

        $response = $this->actingAs($registration['user'])->get(route('platform.settings.edit'));

        $response->assertForbidden();
    }
}
