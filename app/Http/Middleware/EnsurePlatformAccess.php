<?php

namespace App\Http\Middleware;

use App\Support\PermissionScope;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function getPermissionsTeamId;
use function setPermissionsTeamId;

class EnsurePlatformAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $originalTeamId = getPermissionsTeamId();
        setPermissionsTeamId(PermissionScope::PLATFORM);

        try {
            abort_unless($request->user()->can('platform.access'), 403);

            return $next($request);
        } finally {
            setPermissionsTeamId($originalTeamId);
        }
    }
}
