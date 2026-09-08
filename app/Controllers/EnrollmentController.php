<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Mailer;
use App\Helpers\Session;
use App\Middleware\Auth;
use App\Models\AuditLog;
use App\Models\EnrollmentApplication;

class EnrollmentController extends Controller
{
    private EnrollmentApplication $applicationModel;

    /** Grade levels currently offered — keep in sync with sections.grade_level values. */
    private const GRADE_LEVELS = ['Toddler', 'Primary'];

    public function __construct()
    {
        $this->applicationModel = new EnrollmentApplication();
    }

    public function showForm(): void
    {
        Auth::requireRole(['student']);

        $this->view('enrollment/apply', [
            'error'       => Session::flash('error'),
            'old'         => Session::flash('old'),
            'gradeLevels' => self::GRADE_LEVELS,
        ]);
    }

    public function submit(): void
    {
        Auth::requireRole(['student']);

        $studentName   = trim((string) $this->input('student_name'));
        $dateOfBirth   = trim((string) $this->input('date_of_birth'));
        $gradeLevel    = trim((string) $this->input('grade_level'));
        $parentName    = trim((string) $this->input('parent_name'));
        $contactNumber = trim((string) $this->input('contact_number'));
        $address       = trim((string) $this->input('address'));

        $old = compact('studentName', 'dateOfBirth', 'gradeLevel', 'parentName', 'contactNumber', 'address');

        if ($studentName === '' || $dateOfBirth === '' || $gradeLevel === ''
            || $parentName === '' || $contactNumber === '' || $address === '') {
            Session::flash('error', 'All fields are required.');
            Session::flash('old', $old);
            $this->redirect('/enrollment/apply');
        }

        if (!in_array($gradeLevel, self::GRADE_LEVELS, true)) {
            Session::flash('error', 'Please select a valid grade level.');
            Session::flash('old', $old);
            $this->redirect('/enrollment/apply');
        }

        $dobTimestamp = strtotime($dateOfBirth);
        if ($dobTimestamp === false || $dobTimestamp > time()) {
            Session::flash('error', 'Please enter a valid date of birth (not in the future).');
            Session::flash('old', $old);
            $this->redirect('/enrollment/apply');
        }

        $ageYears = (int) floor((time() - $dobTimestamp) / (365.25 * 24 * 3600));
        if ($ageYears > 15) {
            Session::flash('error', 'This system enrolls Montessori-age children. Please double-check the date of birth.');
            Session::flash('old', $old);
            $this->redirect('/enrollment/apply');
        }

        if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $contactNumber)) {
            Session::flash('error', 'Please enter a valid contact number.');
            Session::flash('old', $old);
            $this->redirect('/enrollment/apply');
        }

        $userId = Auth::id();

        $applicationId = $this->applicationModel->createApplication([
            'submitted_by_user_id' => $userId,
            'student_name'         => $studentName,
            'date_of_birth'        => date('Y-m-d', $dobTimestamp),
            'grade_level'          => $gradeLevel,
            'parent_name'          => $parentName,
            'contact_number'       => $contactNumber,
            'address'              => $address,
        ]);

        (new AuditLog())->record($userId, 'enrollment_application_submitted', 'enrollment_applications', $applicationId);

        $userName = Session::get('user_name', 'there');
        $email = $this->currentUserEmail();

        if ($email !== null) {
            Mailer::send(
                $email,
                $userName,
                'SmartEnroll: Application Received',
                $this->confirmationEmailBody($userName, $studentName, $gradeLevel)
            );
        }

        Session::flash('success', "Application for {$studentName} submitted! You'll be notified once it's reviewed.");
        $this->redirect('/my-applications');
    }

    public function myApplications(): void
    {
        Auth::requireRole(['student']);

        $applications = $this->applicationModel->findByUserId((int) Auth::id());

        $this->view('enrollment/my-applications', [
            'applications' => $applications,
            'success'      => Session::flash('success'),
        ]);
    }

    private function currentUserEmail(): ?string
    {
        $userModel = new \App\Models\User();
        $user = $userModel->find((int) Auth::id());
        return $user['email'] ?? null;
    }

    private function confirmationEmailBody(string $parentName, string $studentName, string $gradeLevel): string
    {
        $parent  = htmlspecialchars($parentName);
        $student = htmlspecialchars($studentName);
        $grade   = htmlspecialchars($gradeLevel);

        return <<<HTML
        <div style="font-family: 'Nunito', Arial, sans-serif; background:#EDE7DD; padding:32px;">
            <div style="max-width:480px; margin:0 auto; background:#F5F1E8; border-radius:20px; padding:32px;">
                <h1 style="color:#55704F; font-size:22px; margin-top:0;">Application Received</h1>
                <p style="color:#3B362C; font-size:15px; line-height:1.5;">
                    Hi {$parent}, we've received the enrollment application for <strong>{$student}</strong>
                    ({$grade}). Our team will review it and update the status on your SmartEnroll dashboard.
                </p>
                <p style="color:#8A8272; font-size:13px;">
                    You can check your application status anytime by logging into SmartEnroll.
                </p>
            </div>
        </div>
        HTML;
    }
}
