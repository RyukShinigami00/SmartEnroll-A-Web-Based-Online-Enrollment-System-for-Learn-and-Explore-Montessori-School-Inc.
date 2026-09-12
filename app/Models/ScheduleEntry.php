<?php

namespace App\Models;

use App\Core\Model;

class ScheduleEntry extends Model
{
    protected string $table = 'schedule_entries';

    public function allBySection(int $sectionId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table}
             WHERE section_id = :section_id
             ORDER BY FIELD(day_of_week, 'Mon','Tue','Wed','Thu','Fri','Sat'), start_time"
        );
        $stmt->execute(['section_id' => $sectionId]);
        return $stmt->fetchAll();
    }

    public function createEntry(array $data): int
    {
        return $this->insert([
            'section_id'  => $data['section_id'],
            'subject'     => $data['subject'],
            'day_of_week' => $data['day_of_week'],
            'start_time'  => $data['start_time'],
            'end_time'    => $data['end_time'],
            'room'        => $data['room'],
            'teacher'     => $data['teacher'],
        ]);
    }

    public function updateEntry(int $id, array $data): bool
    {
        return $this->update($id, [
            'subject'     => $data['subject'],
            'day_of_week' => $data['day_of_week'],
            'start_time'  => $data['start_time'],
            'end_time'    => $data['end_time'],
            'room'        => $data['room'],
            'teacher'     => $data['teacher'],
        ]);
    }

    /**
     * Finds an existing entry that overlaps the given section/day/time
     * range, using the standard interval-overlap test:
     * two ranges overlap if one starts before the other ends, both ways.
     * Pass $excludeId when checking an edit, so the entry doesn't
     * conflict with itself.
     */
    public function findConflict(int $sectionId, string $dayOfWeek, string $startTime, string $endTime, ?int $excludeId = null): array|false
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE section_id = :section_id
                  AND day_of_week = :day_of_week
                  AND start_time < :end_time
                  AND end_time > :start_time";
        $params = [
            'section_id'  => $sectionId,
            'day_of_week' => $dayOfWeek,
            'start_time'  => $startTime,
            'end_time'    => $endTime,
        ];

        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params['exclude_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }
}
