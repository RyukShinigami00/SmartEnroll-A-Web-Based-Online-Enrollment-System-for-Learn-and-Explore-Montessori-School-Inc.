<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Middleware\Auth;

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

        $this->view('dashboard/admin', [
            'name' => Session::get('user_name'),
            'role' => Session::get('user_role'),
        ]);
    }
}
