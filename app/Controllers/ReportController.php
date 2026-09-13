<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Middleware\Auth;
use App\Models\EnrollmentApplication;
use App\Models\Section;
use App\Models\Student;

class ReportController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $applicationCounts = (new EnrollmentApplication())->countByStatus();
        $monthlyTrend = (new EnrollmentApplication())->monthlyTrend();
        $sections = (new Section())->allWithCounts();
        $totalStudents = (new Student())->countApproved();

        $this->view('admin/reports', [
            'applicationCounts' => $applicationCounts,
            'monthlyTrend'      => $monthlyTrend,
            'sections'          => $sections,
            'totalStudents'     => $totalStudents,
            'generatedAt'       => date('F j, Y g:i A'),
        ]);
    }
}
