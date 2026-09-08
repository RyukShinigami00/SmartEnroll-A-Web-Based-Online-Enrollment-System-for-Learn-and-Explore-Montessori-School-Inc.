<?php

namespace App\Controllers;

use App\Config\Env;
use App\Core\Controller;
use App\Helpers\Mailer;
use App\Helpers\Session;
use App\Models\AuditLog;
use App\Models\User;

class AuthController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showRegister(): void
    {
        $this->view('auth/register', [
            'error' => Session::flash('error'),
            'old'   => Session::flash('old'),
        ]);
    }

    public function register(): void
    {
        $firstName = trim((string) $this->input('first_name'));
        $lastName  = trim((string) $this->input('last_name'));
        $email     = trim(strtolower((string) $this->input('email')));
        $password  = (string) $this->input('password');
        $confirm   = (string) $this->input('password_confirm');

        $old = ['first_name' => $firstName, 'last_name' => $lastName, 'email' => $email];

        if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
            Session::flash('error', 'All fields are required.');
            Session::flash('old', $old);
            $this->redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            Session::flash('old', $old);
            $this->redirect('/register');
        }

        if ($password !== $confirm) {
            Session::flash('error', 'Passwords do not match.');
            Session::flash('old', $old);
            $this->redirect('/register');
        }

        if (!self::passwordMeetsRequirements($password)) {
            Session::flash('error', 'Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special character.');
            Session::flash('old', $old);
            $this->redirect('/register');
        }

        if ($this->userModel->findByEmail($email)) {
            Session::flash('error', 'An account with that email already exists.');
            Session::flash('old', $old);
            $this->redirect('/register');
        }

        // Public self-registration is always role=student.
        // Admin/Super Admin accounts are provisioned by a Super Admin (Sprint 2).
        $userId = $this->userModel->createUser($firstName, $lastName, $email, $password, 'student');
        $code   = $this->userModel->setVerificationCode($userId);

        (new AuditLog())->record($userId, 'user_registered', 'users', $userId);

        $sent = Mailer::send(
            $email,
            $firstName,
            'Your SmartEnroll verification code',
            Mailer::verificationCodeEmail($firstName, $code)
        );

        // Remember who we're verifying so the code-entry page can pick it up
        // without exposing the address in a URL.
        Session::put('pending_verification_email', $email);

        if (!$sent) {
            Session::flash('error', "Account created, but we couldn't send the verification code right now. Use \"Resend code\" below once mail is configured.");
        }

        $this->redirect('/verify-email');
    }

    public function showLogin(): void
    {
        $this->view('auth/login', [
            'error'   => Session::flash('error'),
            'success' => Session::flash('success'),
        ]);
    }

    public function login(): void
    {
        $email    = trim(strtolower((string) $this->input('email')));
        $password = (string) $this->input('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user || !$this->userModel->verifyPassword($user, $password)) {
            Session::flash('error', 'Invalid email or password.');
            $this->redirect('/login');
        }

        if ((int) $user['is_active'] === 0) {
            Session::flash('error', 'This account has been deactivated. Contact the administrator.');
            $this->redirect('/login');
        }

        if (!$this->userModel->isEmailVerified($user)) {
            Session::put('pending_verification_email', $email);
            Session::flash('error', 'Please verify your email before logging in. Enter the code below, or resend a new one.');
            $this->redirect('/verify-email');
        }

        $this->logUserIn($user);
    }

    public function logout(): void
    {
        if (Session::has('user_id')) {
            (new AuditLog())->record(Session::get('user_id'), 'user_logout', 'users', Session::get('user_id'));
        }

        Session::destroy();
        header('Location: /login');
        exit;
    }

    public function showVerifyEmail(): void
    {
        $this->view('auth/verify', [
            'error'   => Session::flash('error'),
            'success' => Session::flash('success'),
            'email'   => Session::get('pending_verification_email', ''),
        ]);
    }

    public function verifyEmail(): void
    {
        $email = trim(strtolower((string) $this->input('email')));
        $code  = User::normalizeVerificationCode((string) $this->input('code'));

        if ($email === '' || !preg_match('/^\d{6}$/', $code)) {
            Session::flash('error', 'Please enter the 6-digit code sent to your email.');
            $this->redirect('/verify-email');
        }

        $user = $this->userModel->findByEmailAndValidCode($email, $code);

        if (!$user) {
            Session::flash('error', 'That code is invalid or has expired. Request a new one below.');
            Session::put('pending_verification_email', $email);
            $this->redirect('/verify-email');
        }

        $this->userModel->markEmailVerified((int) $user['id']);
        (new AuditLog())->record((int) $user['id'], 'email_verified', 'users', (int) $user['id']);
        Session::forget('pending_verification_email');

        // Verified — log them straight in, no separate login step.
        $this->logUserIn($user);
    }

    public function resendVerification(): void
    {
        $email = trim(strtolower((string) $this->input('email')));
        $user  = $email !== '' ? $this->userModel->findByEmail($email) : false;

        // Always show the same message, whether or not the email exists,
        // to avoid leaking which addresses are registered.
        $genericMessage = "If that email exists and isn't verified yet, we've sent a new code.";

        if ($user && !$this->userModel->isEmailVerified($user)) {
            $code = $this->userModel->setVerificationCode((int) $user['id']);
            Mailer::send(
                $email,
                $user['first_name'],
                'Your SmartEnroll verification code',
                Mailer::verificationCodeEmail($user['first_name'], $code)
            );
        }

        Session::put('pending_verification_email', $email);
        Session::flash('success', $genericMessage);
        $this->redirect('/verify-email');
    }

    /**
     * Shared "establish session + role-based redirect" step used by both
     * a normal password login and a just-completed email verification.
     */
    private function logUserIn(array $user): void
    {
        Session::put('user_id', $user['id']);
        Session::put('user_role', $user['role']);
        Session::put('user_name', $this->userModel->fullName($user));

        (new AuditLog())->record($user['id'], 'user_login', 'users', $user['id']);

        $this->redirect(match ($user['role']) {
            'admin', 'super_admin' => '/admin/dashboard',
            default                => '/dashboard',
        });
    }

    public static function passwordMeetsRequirements(string $password): bool
    {
        return strlen($password) >= 8
            && preg_match('/[A-Z]/', $password)
            && preg_match('/[a-z]/', $password)
            && preg_match('/[0-9]/', $password)
            && preg_match('/[^A-Za-z0-9]/', $password);
    }
}
