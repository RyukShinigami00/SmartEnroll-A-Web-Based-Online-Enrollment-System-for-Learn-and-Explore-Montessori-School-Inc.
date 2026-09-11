<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Middleware\Auth;
use App\Models\AuditLog;
use App\Models\User;

class AdminUserController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(): void
    {
        Auth::requireRole(['super_admin']);

        $this->view('admin/users', [
            'admins'  => $this->userModel->allAdmins(),
            'error'   => Session::flash('error'),
            'success' => Session::flash('success'),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['super_admin']);

        $firstName = trim((string) $this->input('first_name'));
        $lastName  = trim((string) $this->input('last_name'));
        $email     = trim(strtolower((string) $this->input('email')));
        $password  = (string) $this->input('password');
        $role      = $this->input('role');

        if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
            Session::flash('error', 'All fields are required.');
            $this->redirect('/admin/users');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->redirect('/admin/users');
        }

        if (!\App\Controllers\AuthController::passwordMeetsRequirements($password)) {
            Session::flash('error', 'Password must be at least 8 characters and include an uppercase letter, a lowercase letter, a number, and a special character.');
            $this->redirect('/admin/users');
        }

        if (!in_array($role, ['admin', 'super_admin'], true)) {
            Session::flash('error', 'Please select a valid role.');
            $this->redirect('/admin/users');
        }

        if ($this->userModel->findByEmail($email)) {
            Session::flash('error', 'An account with that email already exists.');
            $this->redirect('/admin/users');
        }

        $newUserId = $this->userModel->createUser($firstName, $lastName, $email, $password, $role);

        (new AuditLog())->record((int) Auth::id(), 'admin_account_created', 'users', $newUserId, "Role: {$role}");

        Session::flash('success', "Account created for {$firstName} {$lastName} ({$role}).");
        $this->redirect('/admin/users');
    }

    public function toggleActive(string $id): void
    {
        Auth::requireRole(['super_admin']);

        $userId = (int) $id;

        if ($userId === (int) Auth::id()) {
            Session::flash('error', "You can't deactivate your own account.");
            $this->redirect('/admin/users');
        }

        $target = $this->userModel->find($userId);
        if (!$target) {
            Session::flash('error', 'Account not found.');
            $this->redirect('/admin/users');
        }

        $newState = (int) $target['is_active'] === 1 ? false : true;
        $this->userModel->setActive($userId, $newState);

        (new AuditLog())->record(
            (int) Auth::id(),
            $newState ? 'admin_account_reactivated' : 'admin_account_deactivated',
            'users',
            $userId
        );

        Session::flash('success', $newState ? 'Account reactivated.' : 'Account deactivated.');
        $this->redirect('/admin/users');
    }
}
