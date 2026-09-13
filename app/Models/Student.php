<?php

namespace App\Models;

use App\Core\Model;

class Student extends Model
{
    protected string $table = 'students';

    public function createFromApplication(array $application, int $sectionId): int
    {
        return $this->insert([
            'enrollment_application_id' => $application['id'],
            'user_id'                   => $application['submitted_by_user_id'],
            'name'                      => $application['student_name'],
            'date_of_birth'             => $application['date_of_birth'],
            'grade_level'               => $application['grade_level'],
            'parent_name'               => $application['parent_name'],
            'contact_number'            => $application['contact_number'],
            'address'                   => $application['address'],
            'section_id'                => $sectionId,
            'status'                    => 'approved',
        ]);
    }

    /**
     * All approved children linked to a parent/guardian account
     * (one account can have more than one enrolled child), with
     * their assigned section name attached.
     */
    public function findApprovedByUserId(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT st.*, s.name AS section_name
             FROM {$this->table} st
             LEFT JOIN sections s ON s.id = st.section_id
             WHERE st.user_id = :user_id AND st.status = 'approved'
             ORDER BY st.name"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }
}
