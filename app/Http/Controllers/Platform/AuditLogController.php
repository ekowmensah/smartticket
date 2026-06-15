<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Contracts\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', AuditLog::class);

        return view('platform.audit-logs.index', [
            'auditLogs' => AuditLog::query()
                ->with(['causer', 'subject', 'organization'])
                ->latest()
                ->paginate(25),
        ]);
    }
}
