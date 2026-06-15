<?php

namespace App\Actions\Organizations;

use App\Enums\OrganizationKycStatus;
use App\Models\Organization;
use App\Models\OrganizationKycSubmission;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SubmitKycSubmissionAction
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function execute(Organization $organization, User $actor, array $data): OrganizationKycSubmission
    {
        return DB::transaction(function () use ($organization, $actor, $data): OrganizationKycSubmission {
            $latestSubmission = $organization->latestKycSubmission;

            if ($latestSubmission !== null && in_array($latestSubmission->status, [
                OrganizationKycStatus::SUBMITTED,
                OrganizationKycStatus::UNDER_REVIEW,
                OrganizationKycStatus::APPROVED,
            ], true)) {
                throw ValidationException::withMessages([
                    'submission' => 'This organization already has a KYC submission in a locked state.',
                ]);
            }

            $submission = $latestSubmission ?? new OrganizationKycSubmission([
                'organization_id' => $organization->id,
            ]);

            $submission->fill([
                'submitted_by' => $actor->id,
                'status' => OrganizationKycStatus::SUBMITTED,
                'reviewed_by' => null,
                'submitted_at' => now(),
                'reviewed_at' => null,
                'rejection_reason' => null,
                'business_type' => $data['business_type'],
                'registration_number' => $data['registration_number'] ?? null,
                'tax_identifier' => $data['tax_identifier'] ?? null,
                'legal_name' => $data['legal_name'],
                'contact_name' => $data['contact_name'],
                'contact_phone' => $data['contact_phone'],
                'contact_email' => $data['contact_email'],
                'payout_method' => $data['payout_method'],
                'payout_account_name' => $data['payout_account_name'],
                'payout_account_number' => $data['payout_account_number'],
                'payout_provider' => $data['payout_provider'] ?? null,
            ]);
            $submission->save();

            if (! empty($data['documents'])) {
                $submission->documents()->delete();

                /** @var array<int, array{type:string,file:UploadedFile}> $documents */
                $documents = $data['documents'];

                foreach ($documents as $document) {
                    $storedFile = $document['file']->storeAs(
                        path: "kyc/{$organization->public_id}/{$submission->id}",
                        name: Str::uuid().'.'.$document['file']->getClientOriginalExtension(),
                        options: ['disk' => 'local'],
                    );

                    $submission->documents()->create([
                        'organization_id' => $organization->id,
                        'document_type' => $document['type'],
                        'storage_disk' => 'local',
                        'storage_path' => $storedFile,
                        'original_name' => $document['file']->getClientOriginalName(),
                        'mime_type' => $document['file']->getClientMimeType() ?: 'application/octet-stream',
                        'file_size' => $document['file']->getSize() ?: 0,
                        'uploaded_by' => $actor->id,
                    ]);
                }
            }

            $this->auditLogger->log(
                actor: $actor,
                description: 'Organization KYC submitted',
                event: 'organization_kyc.submitted',
                subject: $submission,
                organizationId: $organization->id,
                properties: [
                    'kyc_submission_id' => $submission->id,
                    'document_count' => $submission->documents()->count(),
                ],
            );

            return $submission->fresh(['documents']);
        });
    }
}
