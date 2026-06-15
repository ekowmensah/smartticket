<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationApprovalStatus;
use App\Enums\OrganizationStatus;
use App\Models\Organization;
use App\Models\User;
use App\Support\AuditLogger;

class ApproveOrganizationAction
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {
    }

    public function execute(Organization $organization, User $actor): Organization
    {
        $organization->forceFill([
            'status' => OrganizationStatus::ACTIVE,
            'approval_status' => OrganizationApprovalStatus::APPROVED,
            'approved_by' => $actor->id,
            'approved_at' => now(),
            'suspended_at' => null,
            'suspension_reason' => null,
        ])->save();

        $this->auditLogger->log(
            actor: $actor,
            description: 'Organization approved',
            event: 'organization.approved',
            subject: $organization,
            organizationId: $organization->id,
            properties: [
                'organization_name' => $organization->name,
            ],
        );

        return $organization->fresh();
    }
}
