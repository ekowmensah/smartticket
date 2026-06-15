<?php

namespace App\Actions\Platform;

use App\Models\Setting;
use App\Models\User;
use App\Support\AuditLogger;
use App\Support\PlatformSettings;

class UpdatePlatformSettingsAction
{
    public function __construct(
        private readonly PlatformSettings $platformSettings,
        private readonly AuditLogger $auditLogger,
    ) {
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function execute(User $actor, array $values): void
    {
        $before = $this->platformSettings->all();
        $this->platformSettings->update($values);
        $after = $this->platformSettings->all();

        $changes = collect($values)
            ->keys()
            ->mapWithKeys(fn (string $key): array => [
                $key => [
                    'old' => $before[$key] ?? null,
                    'new' => $after[$key] ?? null,
                ],
            ])
            ->all();

        $this->auditLogger->log(
            actor: $actor,
            description: 'Platform settings updated',
            event: 'platform_settings.updated',
            subject: Setting::query()->firstWhere([
                'scope' => 'platform',
                'scope_id' => 0,
                'key' => 'product_name',
            ]),
            organizationId: null,
            properties: [
                'changed_keys' => array_keys($values),
                'changes' => $changes,
            ],
        );
    }
}
