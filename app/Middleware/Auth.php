<?php

namespace App\Middleware;

use App\Helpers\Session;

class Auth
{
    /** Block unauthenticated users. */
    public static function requireLogin(): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please log in to continue.');
            header('Location: /login');
            exit;
        }
    }

    /**
     * Block users who don't have one of the allowed roles.
     * Usage: Auth::requireRole(['admin', 'super_admin']);
     */
    public static function requireRole(array $roles): void
    {
        self::requireLogin();

        if (!in_array(Session::get('user_role'), $roles, true)) {
            http_response_code(403);
            require __DIR__ . '/../Views/errors/403.php';
            exit;
        }
    }

    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    public static function role(): ?string
    {
        return Session::get('user_role');
    }

    public static function check(): bool
    {
        return Session::has('user_id');
    }
}
