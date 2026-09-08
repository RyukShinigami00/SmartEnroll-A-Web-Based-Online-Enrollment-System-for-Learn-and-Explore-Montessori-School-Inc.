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
}
