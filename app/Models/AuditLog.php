<?php

namespace App\Models;

use App\Core\Model;

class AuditLog extends Model
{
    protected string $table = 'audit_logs';

    public function record(?int $userId, string $action, ?string $targetTable = null, ?int $targetId = null, ?string $details = null): int
    {
        return $this->insert([
            'user_id'      => $userId,
            'action'       => $action,
            'target_table' => $targetTable,
            'target_id'    => $targetId,
            'details'      => $details,
        ]);
    }
}
