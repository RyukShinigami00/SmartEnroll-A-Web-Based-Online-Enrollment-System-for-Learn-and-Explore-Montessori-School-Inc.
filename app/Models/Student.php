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

    public function countApproved(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table} WHERE status = 'approved'");
        return (int) $stmt->fetchColumn();
    }

    /**
     * All enrolled students for the admin Student Records page, optionally
     * filtered by grade level and/or a free-text name search.
     */
    public function allForAdmin(?string $gradeLevel = null, ?string $search = null): array
    {
        $sql = "SELECT st.*, s.name AS section_name
                FROM {$this->table} st
                LEFT JOIN sections s ON s.id = st.section_id";
        $conditions = [];
        $params = [];

        if ($gradeLevel !== null) {
            $conditions[] = "st.grade_level = :grade_level";
            $params['grade_level'] = $gradeLevel;
        }

        if ($search !== null && $search !== '') {
            $conditions[] = "(st.name LIKE :search1 OR st.parent_name LIKE :search2)";
            $params['search1'] = '%' . $search . '%';
            $params['search2'] = '%' . $search . '%';
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY st.name";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findWithSection(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT st.*, s.name AS section_name
             FROM {$this->table} st
             LEFT JOIN sections s ON s.id = st.section_id
             WHERE st.id = :id
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function updateDetails(int $id, string $contactNumber, string $address, int $sectionId): bool
    {
        return $this->update($id, [
            'contact_number' => $contactNumber,
            'address'        => $address,
            'section_id'     => $sectionId,
        ]);
    }
}
