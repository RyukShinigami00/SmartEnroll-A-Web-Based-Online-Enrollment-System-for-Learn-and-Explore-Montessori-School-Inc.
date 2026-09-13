<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Middleware\Auth;
use App\Models\EnrollmentApplication;
use App\Models\Section;
use App\Models\Student;

class DashboardController extends Controller
{
    public function student(): void
    {
        Auth::requireLogin();

        $this->view('dashboard/student', [
            'name' => Session::get('user_name'),
        ]);
    }

    public function admin(): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $applicationCounts = (new EnrollmentApplication())->countByStatus();
        $sectionTotals = (new Section())->totals();
        $totalStudents = (new Student())->countApproved();

        $this->view('dashboard/admin', [
            'name'              => Session::get('user_name'),
            'role'              => Session::get('user_role'),
            'applicationCounts' => $applicationCounts,
            'sectionTotals'     => $sectionTotals,
            'totalStudents'     => $totalStudents,
        ]);
    }
}
