<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Contracts\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        $this->authorize('review', new Organization());

        return view('platform.organizations.index', [
            'organizations' => Organization::query()
                ->with(['creator', 'latestKycSubmission'])
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function show(Organization $organization): View
    {
        $this->authorize('review', $organization);

        $organization->load([
            'creator',
            'approver',
            'latestKycSubmission.documents',
            'latestKycSubmission.submitter',
            'latestKycSubmission.reviewer',
        ]);

        return view('platform.organizations.show', [
            'organization' => $organization,
            'latestKycSubmission' => $organization->latestKycSubmission,
        ]);
    }
}
