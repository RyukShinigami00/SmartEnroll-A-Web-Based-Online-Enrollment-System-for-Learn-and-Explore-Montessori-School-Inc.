<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\SchedulePdf;
use App\Middleware\Auth;
use App\Models\ScheduleEntry;
use App\Models\Student;

class StudentScheduleController extends Controller
{
    private Student $studentModel;
    private ScheduleEntry $scheduleModel;

    public function __construct()
    {
        $this->studentModel = new Student();
        $this->scheduleModel = new ScheduleEntry();
    }

    public function index(): void
    {
        Auth::requireRole(['student']);

        $children = $this->studentModel->findApprovedByUserId((int) Auth::id());

        $schedules = [];
        foreach ($children as $child) {
            $schedules[] = [
                'student' => $child,
                'entries' => $this->scheduleModel->allBySection((int) $child['section_id']),
            ];
        }

        $this->view('student/my-schedule', [
            'schedules' => $schedules,
        ]);
    }

    public function downloadPdf(string $studentId): void
    {
        Auth::requireRole(['student']);

        $student = $this->studentModel->find((int) $studentId);

        // A student can only ever download their own child's schedule —
        // never trust the ID in the URL without checking ownership.
        if (!$student || (int) $student['user_id'] !== (int) Auth::id() || $student['status'] !== 'approved') {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $entries = $this->scheduleModel->allBySection((int) $student['section_id']);
        $sectionName = $student['section_id'] ? $this->sectionName((int) $student['section_id']) : '';

        $pdfContent = SchedulePdf::generate($student['name'], $sectionName, $entries);

        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student['name']) . '_schedule.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfContent));
        echo $pdfContent;
        exit;
    }

    private function sectionName(int $sectionId): string
    {
        $section = (new \App\Models\Section())->find($sectionId);
        return $section['name'] ?? '';
    }
}
