<?php

namespace App\Http\Controllers\Organizer;

use App\Actions\Organizations\SubmitKycSubmissionAction;
use App\Enums\OrganizationKycStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\StoreKycSubmissionRequest;
use App\Models\Organization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KycSubmissionController extends Controller
{
    public function edit(Request $request, Organization $organization): View
    {
        $this->authorize('submitKyc', $organization);

        $submission = $organization->latestKycSubmission;

        return view('organizer.kyc.edit', [
            'organization' => $organization,
            'submission' => $submission,
            'isLocked' => $submission !== null && in_array($submission->status, [
                OrganizationKycStatus::SUBMITTED,
                OrganizationKycStatus::UNDER_REVIEW,
                OrganizationKycStatus::APPROVED,
            ], true),
        ]);
    }

    public function update(
        StoreKycSubmissionRequest $request,
        Organization $organization,
        SubmitKycSubmissionAction $submitKycSubmissionAction,
    ): RedirectResponse {
        $this->authorize('submitKyc', $organization);

        $submitKycSubmissionAction->execute(
            organization: $organization,
            actor: $request->user(),
            data: $request->validated(),
        );

        return redirect()
            ->route('organizer.kyc.edit', $organization)
            ->with('status', 'KYC submission sent for platform review.');
    }
}
