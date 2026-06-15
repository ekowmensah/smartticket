<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, Organization $organization): View
    {
        $this->authorize('viewDashboard', $organization);

        return view('organizer.dashboard', [
            'organization' => $organization,
            'membership' => $request->attributes->get('organization_membership'),
        ]);
    }
}
