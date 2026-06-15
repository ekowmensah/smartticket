<?php

namespace App\Http\Middleware;

use App\Enums\OrganizationMembershipStatus;
use App\Enums\OrganizationStatus;
use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function getPermissionsTeamId;
use function setPermissionsTeamId;

class EnsureOrganizationAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Organization $organization */
        $organization = $request->route('organization');

        $membership = $request->user()
            ->memberships()
            ->where('organization_id', $organization->id)
            ->first();

        abort_if($membership === null, 403);
        abort_if($membership->status !== OrganizationMembershipStatus::ACTIVE, 403);
        abort_if($organization->status === OrganizationStatus::SUSPENDED, 403);

        $originalTeamId = getPermissionsTeamId();
        setPermissionsTeamId($organization->id);

        $request->attributes->set('current_organization', $organization);
        $request->attributes->set('organization_membership', $membership);

        try {
            return $next($request);
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }
}
