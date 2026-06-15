<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('platform.dashboard', [
            'organizationCount' => Organization::query()->count(),
            'pendingOrganizationCount' => Organization::query()->where('approval_status', 'pending')->count(),
            'userCount' => User::query()->count(),
        ]);
    }
}
