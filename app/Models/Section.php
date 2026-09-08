<?php

namespace App\Models;

use App\Core\Model;

class Section extends Model
{
    protected string $table = 'sections';

    public function allWithCounts(): array
    {
        $stmt = $this->db->query(
            "SELECT s.*, COUNT(st.id) AS student_count
             FROM sections s
             LEFT JOIN students st ON st.section_id = s.id AND st.status = 'approved'
             GROUP BY s.id
             ORDER BY s.grade_level, s.name"
        );
        return $stmt->fetchAll();
    }

    /**
     * Sections matching a grade level, with remaining capacity, for the
     * admin's "assign to section" dropdown when approving an application.
     */
    public function byGradeLevelWithSpace(string $gradeLevel): array
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, COUNT(st.id) AS student_count
             FROM sections s
             LEFT JOIN students st ON st.section_id = s.id AND st.status = 'approved'
             WHERE s.grade_level = :grade_level
             GROUP BY s.id
             ORDER BY s.name"
        );
        $stmt->execute(['grade_level' => $gradeLevel]);
        return $stmt->fetchAll();
    }

    public function createSection(string $gradeLevel, string $name, int $capacity): int
    {
        return $this->insert([
            'grade_level' => $gradeLevel,
            'name'        => $name,
            'capacity'    => $capacity,
        ]);
    }
}
