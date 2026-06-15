<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationKycStatus;
use App\Models\OrganizationKycSubmission;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class ReviewKycSubmissionAction
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {
    }

    /**
     * @param  array{action:string,rejection_reason?:string|null}  $data
     */
    public function execute(OrganizationKycSubmission $submission, User $actor, array $data): OrganizationKycSubmission
    {
        if (! in_array($data['action'], ['approve', 'reject'], true)) {
            throw new InvalidArgumentException('Unsupported KYC review action.');
        }

        $status = $data['action'] === 'approve'
            ? OrganizationKycStatus::APPROVED
            : OrganizationKycStatus::REJECTED;

        $submission->forceFill([
            'status' => $status,
            'reviewed_by' => $actor->id,
            'reviewed_at' => now(),
            'rejection_reason' => $status === OrganizationKycStatus::REJECTED
                ? Arr::get($data, 'rejection_reason')
                : null,
        ])->save();

        $this->auditLogger->log(
            actor: $actor,
            description: $status === OrganizationKycStatus::APPROVED
                ? 'Organization KYC approved'
                : 'Organization KYC rejected',
            event: $status === OrganizationKycStatus::APPROVED
                ? 'organization_kyc.approved'
                : 'organization_kyc.rejected',
            subject: $submission,
            organizationId: $submission->organization_id,
            properties: [
                'kyc_submission_id' => $submission->id,
                'status' => $submission->status->value,
                'rejection_reason' => $submission->rejection_reason,
            ],
        );

        return $submission->fresh(['documents', 'organization']);
    }
}
