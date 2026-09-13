<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Middleware\Auth;
use App\Models\AuditLog;
use App\Models\Section;
use App\Models\Student;

class StudentController extends Controller
{
    private Student $studentModel;
    private Section $sectionModel;

    private const GRADE_LEVELS = ['Toddler', 'Primary'];

    public function __construct()
    {
        $this->studentModel = new Student();
        $this->sectionModel = new Section();
    }

    public function index(): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $gradeLevel = $this->input('grade_level');
        $gradeLevel = in_array($gradeLevel, self::GRADE_LEVELS, true) ? $gradeLevel : null;
        $search = trim((string) $this->input('search', ''));

        $students = $this->studentModel->allForAdmin($gradeLevel, $search !== '' ? $search : null);

        $this->view('admin/students-index', [
            'students'    => $students,
            'activeGrade' => $gradeLevel ?? '',
            'search'      => $search,
            'gradeLevels' => self::GRADE_LEVELS,
            'success'     => Session::flash('success'),
        ]);
    }

    public function edit(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $student = $this->studentModel->findWithSection((int) $id);

        if (!$student) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $sections = $this->sectionModel->byGradeLevelWithSpace($student['grade_level']);

        $this->view('admin/student-edit', [
            'student'  => $student,
            'sections' => $sections,
            'error'    => Session::flash('error'),
        ]);
    }

    public function update(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $studentId = (int) $id;
        $student = $this->studentModel->findWithSection($studentId);

        if (!$student) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $contactNumber = trim((string) $this->input('contact_number'));
        $address = trim((string) $this->input('address'));
        $sectionId = (int) $this->input('section_id');

        if ($contactNumber === '' || $address === '' || $sectionId <= 0) {
            Session::flash('error', 'All fields are required.');
            $this->redirect("/admin/students/{$studentId}/edit");
        }

        if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $contactNumber)) {
            Session::flash('error', 'Please enter a valid contact number.');
            $this->redirect("/admin/students/{$studentId}/edit");
        }

        // If moving to a different section, make sure it actually has room.
        if ($sectionId !== (int) $student['section_id']) {
            $target = null;
            foreach ($this->sectionModel->byGradeLevelWithSpace($student['grade_level']) as $s) {
                if ((int) $s['id'] === $sectionId) {
                    $target = $s;
                    break;
                }
            }

            if (!$target) {
                Session::flash('error', 'Selected section not found.');
                $this->redirect("/admin/students/{$studentId}/edit");
            }

            if ((int) $target['student_count'] >= (int) $target['capacity']) {
                Session::flash('error', "{$target['name']} is already at capacity. Choose a different section.");
                $this->redirect("/admin/students/{$studentId}/edit");
            }
        }

        $this->studentModel->updateDetails($studentId, $contactNumber, $address, $sectionId);

        (new AuditLog())->record((int) Auth::id(), 'student_record_updated', 'students', $studentId);

        Session::flash('success', "Updated {$student['name']}'s record.");
        $this->redirect('/admin/students');
    }

    public function exportCsv(): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $students = $this->studentModel->allForAdmin();

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="students_' . date('Y-m-d') . '.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Name', 'Date of Birth', 'Grade Level', 'Section', 'Parent Name', 'Contact Number', 'Address', 'Enrolled Since']);

        foreach ($students as $student) {
            fputcsv($out, [
                $student['name'],
                $student['date_of_birth'],
                $student['grade_level'],
                $student['section_name'] ?? '—',
                $student['parent_name'],
                $student['contact_number'],
                $student['address'],
                $student['created_at'],
            ]);
        }

        fclose($out);

        (new AuditLog())->record((int) Auth::id(), 'students_exported_csv');
        exit;
    }
}
