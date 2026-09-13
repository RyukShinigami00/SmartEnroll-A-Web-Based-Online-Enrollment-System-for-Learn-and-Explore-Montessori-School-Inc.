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

    /**
     * A single section with its live student count, for the edit/delete
     * screens (grade_level is intentionally not editable — changing it
     * after students are assigned would silently orphan the capacity logic).
     */
    public function findWithCount(int $id): array|false
    {
        $stmt = $this->db->prepare(
            "SELECT s.*, COUNT(st.id) AS student_count
             FROM sections s
             LEFT JOIN students st ON st.section_id = s.id AND st.status = 'approved'
             WHERE s.id = :id
             GROUP BY s.id"
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function updateSection(int $id, string $name, int $capacity): bool
    {
        return $this->update($id, [
            'name'     => $name,
            'capacity' => $capacity,
        ]);
    }

    public function deleteSection(int $id): bool
    {
        return $this->delete($id);
    }

    /**
     * Total capacity and total currently-enrolled students across all
     * sections, for the admin dashboard's utilization metric.
     */
    public function totals(): array
    {
        $stmt = $this->db->query(
            "SELECT
                COALESCE(SUM(s.capacity), 0) AS total_capacity,
                COUNT(DISTINCT st.id) AS total_enrolled,
                COUNT(DISTINCT s.id) AS total_sections
             FROM sections s
             LEFT JOIN students st ON st.section_id = s.id AND st.status = 'approved'"
        );
        return $stmt->fetch();
    }
}
