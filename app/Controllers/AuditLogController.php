<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\Auth;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['super_admin']);

        $logs = (new AuditLog())->allWithUser();

        $this->view('admin/audit-log', [
            'logs' => $logs,
        ]);
    }
}
