<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Env;
use App\Controllers\AdminEnrollmentController;
use App\Controllers\AdminUserController;
use App\Controllers\AuditLogController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\EnrollmentController;
use App\Controllers\HomeController;
use App\Controllers\SectionController;
use App\Core\Router;
use App\Helpers\Session;

Env::load(__DIR__ . '/../.env');
Session::start();

// Basic security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

$router = new Router();

// Public landing page
$router->get('/', [HomeController::class, 'index']);

// Auth
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// Dashboards (role-gated inside the controller via Auth middleware)
$router->get('/dashboard', [DashboardController::class, 'student']);
$router->get('/admin/dashboard', [DashboardController::class, 'admin']);

// Enrollment (student-only, role-gated inside the controller)
$router->get('/enrollment/apply', [EnrollmentController::class, 'showForm']);
$router->post('/enrollment/apply', [EnrollmentController::class, 'submit']);
$router->get('/my-applications', [EnrollmentController::class, 'myApplications']);

// Admin: enrollment review (admin/super_admin only, role-gated inside the controller)
$router->get('/admin/applications', [AdminEnrollmentController::class, 'index']);
$router->get('/admin/applications/{id}', [AdminEnrollmentController::class, 'show']);
$router->post('/admin/applications/{id}/approve', [AdminEnrollmentController::class, 'approve']);
$router->post('/admin/applications/{id}/reject', [AdminEnrollmentController::class, 'reject']);

// Admin: section management
$router->get('/admin/sections', [SectionController::class, 'index']);
$router->post('/admin/sections', [SectionController::class, 'store']);
$router->get('/admin/sections/{id}/edit', [SectionController::class, 'edit']);
$router->post('/admin/sections/{id}', [SectionController::class, 'update']);
$router->post('/admin/sections/{id}/delete', [SectionController::class, 'destroy']);

// Super Admin only: audit log and admin account management
$router->get('/admin/audit-log', [AuditLogController::class, 'index']);
$router->get('/admin/users', [AdminUserController::class, 'index']);
$router->post('/admin/users', [AdminUserController::class, 'store']);
$router->post('/admin/users/{id}/toggle', [AdminUserController::class, 'toggleActive']);

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
