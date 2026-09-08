<?php

namespace App\Models;

use App\Core\Model;

class EnrollmentApplication extends Model
{
    protected string $table = 'enrollment_applications';

    public function createApplication(array $data): int
    {
        return $this->insert([
            'submitted_by_user_id' => $data['submitted_by_user_id'],
            'student_name'         => $data['student_name'],
            'date_of_birth'        => $data['date_of_birth'],
            'grade_level'          => $data['grade_level'],
            'parent_name'          => $data['parent_name'],
            'contact_number'       => $data['contact_number'],
            'address'              => $data['address'],
        ]);
    }

    /**
     * All applications submitted by a given user, most recent first.
     */
    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT ea.*, s.name AS section_name
             FROM {$this->table} ea
             LEFT JOIN sections s ON s.id = ea.assigned_section_id
             WHERE ea.submitted_by_user_id = :user_id
             ORDER BY ea.created_at DESC"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findByIdAndUserId(int $id, int $userId): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id AND submitted_by_user_id = :user_id LIMIT 1"
        );
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        return $stmt->fetch();
    }

    /**
     * All applications for admin review, optionally filtered by status,
     * most recent first, with the applicant's account email attached.
     */
    public function allForAdmin(?string $status = null): array
    {
        $sql = "SELECT ea.*, u.email AS applicant_email
                FROM {$this->table} ea
                LEFT JOIN users u ON u.id = ea.submitted_by_user_id";
        $params = [];

        if ($status !== null) {
            $sql .= " WHERE ea.status = :status";
            $params['status'] = $status;
        }

        $sql .= " ORDER BY ea.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findWithApplicantEmail(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT ea.*, u.email AS applicant_email
             FROM {$this->table} ea
             LEFT JOIN users u ON u.id = ea.submitted_by_user_id
             WHERE ea.id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function approve(int $id, int $sectionId, int $reviewerId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET status = 'approved',
                 assigned_section_id = :section_id,
                 reviewed_by_user_id = :reviewer_id,
                 reviewed_at = NOW(),
                 reject_reason = NULL
             WHERE id = :id"
        );
        return $stmt->execute(['section_id' => $sectionId, 'reviewer_id' => $reviewerId, 'id' => $id]);
    }

    public function reject(int $id, string $reason, int $reviewerId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table}
             SET status = 'rejected',
                 reject_reason = :reason,
                 reviewed_by_user_id = :reviewer_id,
                 reviewed_at = NOW()
             WHERE id = :id"
        );
        return $stmt->execute(['reason' => $reason, 'reviewer_id' => $reviewerId, 'id' => $id]);
    }
}
