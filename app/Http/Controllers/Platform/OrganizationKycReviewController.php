<?php

namespace App\Http\Controllers\Platform;

use App\Actions\Organizations\ReviewKycSubmissionAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\ReviewKycSubmissionRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class OrganizationKycReviewController extends Controller
{
    public function update(
        ReviewKycSubmissionRequest $request,
        Organization $organization,
        ReviewKycSubmissionAction $reviewKycSubmissionAction,
    ): RedirectResponse {
        $this->authorize('reviewKyc', $organization);

        $submission = $organization->latestKycSubmission;

        abort_if($submission === null, 404);

        $reviewKycSubmissionAction->execute(
            submission: $submission,
            actor: $request->user(),
            data: $request->validated(),
        );

        return redirect()
            ->route('platform.organizations.show', $organization)
            ->with('status', 'KYC review updated.');
    }
}
