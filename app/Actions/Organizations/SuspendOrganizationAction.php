<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationStatus;
use App\Models\Organization;
use App\Models\User;
use App\Support\AuditLogger;

class SuspendOrganizationAction
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {
    }

    public function execute(Organization $organization, User $actor, string $reason): Organization
    {
        $organization->forceFill([
            'status' => OrganizationStatus::SUSPENDED,
            'suspended_at' => now(),
            'suspension_reason' => $reason,
        ])->save();

        $this->auditLogger->log(
            actor: $actor,
            description: 'Organization suspended',
            event: 'organization.suspended',
            subject: $organization,
            organizationId: $organization->id,
            properties: [
                'organization_name' => $organization->name,
                'reason' => $reason,
            ],
        );

        return $organization->fresh();
    }
}
