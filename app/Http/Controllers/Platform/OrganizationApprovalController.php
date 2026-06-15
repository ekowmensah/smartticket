<?php

namespace App\Http\Controllers\Platform;

use App\Actions\Organizations\ApproveOrganizationAction;
use App\Actions\Organizations\SuspendOrganizationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\ReviewOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;

class OrganizationApprovalController extends Controller
{
    public function update(
        ReviewOrganizationRequest $request,
        Organization $organization,
        ApproveOrganizationAction $approveOrganizationAction,
        SuspendOrganizationAction $suspendOrganizationAction,
    ): RedirectResponse {
        $this->authorize('review', $organization);

        if ($request->validated('action') === 'approve') {
            $approveOrganizationAction->execute($organization, $request->user());
        } else {
            $suspendOrganizationAction->execute(
                organization: $organization,
                actor: $request->user(),
                reason: (string) $request->validated('reason'),
            );
        }

        return redirect()
            ->route('platform.organizations.show', $organization)
            ->with('status', 'Organization review updated.');
    }
}
