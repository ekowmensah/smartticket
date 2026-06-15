<?php

namespace App\Http\Controllers\Organizer;

use App\Actions\Organizations\CreateOrganizationInvitationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Organizer\StoreOrganizationInvitationRequest;
use App\Models\Organization;
use App\Support\OrganizationRoleCatalog;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index(Request $request, Organization $organization): View
    {
        $this->authorize('viewTeam', $organization);

        $canManageTeam = $request->user()->can('inviteTeam', $organization);

        $organization->load([
            'memberships.user',
            'memberships.inviter',
            'invitations.inviter',
        ]);

        return view('organizer.team.index', [
            'organization' => $organization,
            'canManageTeam' => $canManageTeam,
            'assignableRoles' => OrganizationRoleCatalog::assignableRoles(),
        ]);
    }

    public function store(
        StoreOrganizationInvitationRequest $request,
        Organization $organization,
        CreateOrganizationInvitationAction $createOrganizationInvitationAction,
    ): RedirectResponse {
        $this->authorize('inviteTeam', $organization);

        $result = $createOrganizationInvitationAction->execute(
            organization: $organization,
            actor: $request->user(),
            data: $request->validated(),
        );

        return redirect()
            ->route('organizer.team.index', $organization)
            ->with('status', 'Invitation created successfully.')
            ->with('invitation_link', route('invitations.show', ['token' => $result['token']]));
    }
}
