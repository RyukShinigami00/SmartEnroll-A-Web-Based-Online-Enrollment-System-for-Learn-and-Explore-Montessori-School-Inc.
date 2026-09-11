<?php

namespace App\Controllers;

use App\Core\Controller;
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
        // Admin/Super Admin accounts are provisioned by a Super Admin.
        $userId = $this->userModel->createUser($firstName, $lastName, $email, $password, 'student');

        (new AuditLog())->record($userId, 'user_registered', 'users', $userId);

        $user = $this->userModel->find($userId);
        $this->logUserIn($user);
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

    /**
     * Shared "establish session + role-based redirect" step used after
     * either a successful registration or a normal password login.
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
