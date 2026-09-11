<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Mailer;
use App\Helpers\Session;
use App\Middleware\Auth;
use App\Models\AuditLog;
use App\Models\EnrollmentApplication;
use App\Models\Section;
use App\Models\Student;

class AdminEnrollmentController extends Controller
{
    private EnrollmentApplication $applicationModel;
    private Section $sectionModel;

    public function __construct()
    {
        $this->applicationModel = new EnrollmentApplication();
        $this->sectionModel = new Section();
    }

    /** Grade levels currently offered — keep in sync with sections.grade_level values. */
    private const GRADE_LEVELS = ['Toddler', 'Primary'];

    public function index(): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $status = $this->input('status'); // null, 'pending', 'approved', or 'rejected'
        $status = in_array($status, ['pending', 'approved', 'rejected'], true) ? $status : null;

        $gradeLevel = $this->input('grade_level');
        $gradeLevel = in_array($gradeLevel, self::GRADE_LEVELS, true) ? $gradeLevel : null;

        $search = trim((string) $this->input('search', ''));

        $applications = $this->applicationModel->allForAdmin($status, $gradeLevel, $search !== '' ? $search : null);

        $this->view('admin/applications-index', [
            'applications' => $applications,
            'activeStatus' => $status ?? 'all',
            'activeGrade'  => $gradeLevel ?? '',
            'search'       => $search,
            'gradeLevels'  => self::GRADE_LEVELS,
            'success'      => Session::flash('success'),
            'error'        => Session::flash('error'),
        ]);
    }

    public function show(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $application = $this->applicationModel->findWithApplicantEmail((int) $id);

        if (!$application) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $sections = $this->sectionModel->byGradeLevelWithSpace($application['grade_level']);

        $this->view('admin/application-show', [
            'application' => $application,
            'sections'    => $sections,
            'error'       => Session::flash('error'),
        ]);
    }

    public function approve(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $applicationId = (int) $id;
        $sectionId = (int) $this->input('section_id');

        $application = $this->applicationModel->findWithApplicantEmail($applicationId);

        if (!$application || $application['status'] !== 'pending') {
            Session::flash('error', 'This application has already been reviewed or does not exist.');
            $this->redirect('/admin/applications');
        }

        if ($sectionId <= 0) {
            Session::flash('error', 'Please select a section to assign.');
            $this->redirect("/admin/applications/{$applicationId}");
        }

        $section = null;
        foreach ($this->sectionModel->byGradeLevelWithSpace($application['grade_level']) as $s) {
            if ((int) $s['id'] === $sectionId) {
                $section = $s;
                break;
            }
        }

        if (!$section) {
            Session::flash('error', 'Selected section not found.');
            $this->redirect("/admin/applications/{$applicationId}");
        }

        if ((int) $section['student_count'] >= (int) $section['capacity']) {
            Session::flash('error', "{$section['name']} is already at capacity ({$section['capacity']} students). Choose a different section.");
            $this->redirect("/admin/applications/{$applicationId}");
        }

        $reviewerId = (int) Auth::id();

        (new Student())->createFromApplication($application, $sectionId);
        $this->applicationModel->approve($applicationId, $sectionId, $reviewerId);

        (new AuditLog())->record($reviewerId, 'enrollment_application_approved', 'enrollment_applications', $applicationId, "Assigned to section #{$sectionId}");

        if (!empty($application['applicant_email'])) {
            Mailer::send(
                $application['applicant_email'],
                $application['parent_name'],
                'SmartEnroll: Application Approved!',
                $this->approvalEmailBody($application['parent_name'], $application['student_name'], $section['name'])
            );
        }

        Session::flash('success', "Application for {$application['student_name']} approved and assigned to {$section['name']}.");
        $this->redirect('/admin/applications');
    }

    public function reject(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $applicationId = (int) $id;
        $reason = trim((string) $this->input('reject_reason'));

        $application = $this->applicationModel->findWithApplicantEmail($applicationId);

        if (!$application || $application['status'] !== 'pending') {
            Session::flash('error', 'This application has already been reviewed or does not exist.');
            $this->redirect('/admin/applications');
        }

        if ($reason === '') {
            Session::flash('error', 'Please provide a reason for rejection.');
            $this->redirect("/admin/applications/{$applicationId}");
        }

        $reviewerId = (int) Auth::id();

        $this->applicationModel->reject($applicationId, $reason, $reviewerId);
        (new AuditLog())->record($reviewerId, 'enrollment_application_rejected', 'enrollment_applications', $applicationId, $reason);

        if (!empty($application['applicant_email'])) {
            Mailer::send(
                $application['applicant_email'],
                $application['parent_name'],
                'SmartEnroll: Application Update',
                $this->rejectionEmailBody($application['parent_name'], $application['student_name'], $reason)
            );
        }

        Session::flash('success', "Application for {$application['student_name']} has been rejected.");
        $this->redirect('/admin/applications');
    }

    private function approvalEmailBody(string $parentName, string $studentName, string $sectionName): string
    {
        $parent  = htmlspecialchars($parentName);
        $student = htmlspecialchars($studentName);
        $section = htmlspecialchars($sectionName);

        return <<<HTML
        <div style="font-family: 'Nunito', Arial, sans-serif; background:#EDE7DD; padding:32px;">
            <div style="max-width:480px; margin:0 auto; background:#F5F1E8; border-radius:20px; padding:32px;">
                <h1 style="color:#55704F; font-size:22px; margin-top:0;">Great news, {$parent}!</h1>
                <p style="color:#3B362C; font-size:15px; line-height:1.5;">
                    <strong>{$student}</strong>'s enrollment application has been approved and assigned to
                    <strong>{$section}</strong>. Welcome to LEMS!
                </p>
                <p style="color:#8A8272; font-size:13px;">
                    Log in to SmartEnroll to view more details.
                </p>
            </div>
        </div>
        HTML;
    }

    private function rejectionEmailBody(string $parentName, string $studentName, string $reason): string
    {
        $parent  = htmlspecialchars($parentName);
        $student = htmlspecialchars($studentName);
        $reasonEscaped = htmlspecialchars($reason);

        return <<<HTML
        <div style="font-family: 'Nunito', Arial, sans-serif; background:#EDE7DD; padding:32px;">
            <div style="max-width:480px; margin:0 auto; background:#F5F1E8; border-radius:20px; padding:32px;">
                <h1 style="color:#55704F; font-size:22px; margin-top:0;">Application Update</h1>
                <p style="color:#3B362C; font-size:15px; line-height:1.5;">
                    Hi {$parent}, we're unable to move forward with {$student}'s enrollment application
                    at this time.
                </p>
                <p style="color:#3B362C; font-size:14px; line-height:1.5; background:#F5DED4; padding:12px 16px; border-radius:12px;">
                    {$reasonEscaped}
                </p>
                <p style="color:#8A8272; font-size:13px;">
                    Feel free to reach out to the school office with any questions, or submit a new application.
                </p>
            </div>
        </div>
        HTML;
    }
}
