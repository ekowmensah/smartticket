<?php

namespace App\Http\Controllers\Public;

use App\Actions\Organizations\AcceptOrganizationInvitationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\AcceptOrganizationInvitationRequest;
use App\Models\OrganizationInvitation;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizationInvitationController extends Controller
{
    public function show(Request $request, string $token): View
    {
        $invitation = OrganizationInvitation::fromToken($token)->load('organization', 'inviter');

        abort_if($invitation->isExpired(), 410);
        abort_if($invitation->isAccepted(), 410);

        $existingUser = \App\Models\User::query()->where('email', $invitation->email)->first();

        return view('public.invitations.show', [
            'invitation' => $invitation,
            'token' => $token,
            'existingUser' => $existingUser,
            'authUser' => $request->user(),
        ]);
    }

    public function store(
        AcceptOrganizationInvitationRequest $request,
        string $token,
        AcceptOrganizationInvitationAction $acceptOrganizationInvitationAction,
    ): RedirectResponse {
        $invitation = OrganizationInvitation::fromToken($token)->load('organization');

        abort_if($invitation->isExpired(), 410);
        abort_if($invitation->isAccepted(), 410);

        $user = $acceptOrganizationInvitationAction->execute(
            invitation: $invitation,
            authenticatedUser: $request->user(),
            data: $request->validated(),
        );

        if (! $request->user()) {
            Auth::login($user);
        }

        return redirect()
            ->route('organizer.dashboard', $invitation->organization)
            ->with('status', 'Invitation accepted successfully.');
    }
}
