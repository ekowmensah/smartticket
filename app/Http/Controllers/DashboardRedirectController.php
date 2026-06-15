<?php

namespace App\Http\Controllers;

use App\Support\PermissionScope;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use function setPermissionsTeamId;

class DashboardRedirectController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        setPermissionsTeamId(PermissionScope::PLATFORM);

        if ($user->can('platform.access')) {
            return redirect()->route('platform.dashboard');
        }

        $organization = $user->organizations()->orderBy('organizations.name')->first();

        if ($organization !== null) {
            return redirect()->route('organizer.dashboard', $organization);
        }

        return redirect()->route('profile.edit');
    }
}
