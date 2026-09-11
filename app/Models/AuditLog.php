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

    /**
     * All audit log entries, most recent first, with the acting user's
     * name and email attached (Super Admin only — see AuditLogController).
     */
    public function allWithUser(int $limit = 200): array
    {
        $stmt = $this->db->prepare(
            "SELECT al.*, u.first_name, u.last_name, u.email
             FROM {$this->table} al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
