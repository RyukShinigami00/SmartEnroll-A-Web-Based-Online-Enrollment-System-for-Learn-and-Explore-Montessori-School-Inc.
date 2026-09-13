<?php

namespace App\Controllers;

use App\Config\Database;
use App\Core\Controller;
use App\Helpers\Session;
use App\Middleware\Auth;
use App\Models\AuditLog;
use App\Models\Setting;

class SettingsController extends Controller
{
    /** Tables included in the manual backup export, in FK-safe order. */
    private const BACKUP_TABLES = [
        'users', 'sections', 'enrollment_applications', 'students',
        'schedule_entries', 'audit_logs', 'settings',
    ];

    public function index(): void
    {
        Auth::requireRole(['super_admin']);

        $this->view('admin/settings', [
            'settings' => (new Setting())->getAll(),
            'success'  => Session::flash('success'),
            'error'    => Session::flash('error'),
        ]);
    }

    public function update(): void
    {
        Auth::requireRole(['super_admin']);

        $schoolName = trim((string) $this->input('school_name'));
        $schoolEmail = trim((string) $this->input('school_email'));
        $schoolContact = trim((string) $this->input('school_contact_number'));

        if ($schoolName === '' || !filter_var($schoolEmail, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please provide a school name and a valid contact email.');
            $this->redirect('/admin/settings');
        }

        $settingModel = new Setting();
        $settingModel->set('school_name', $schoolName);
        $settingModel->set('school_email', $schoolEmail);
        $settingModel->set('school_contact_number', $schoolContact);

        (new AuditLog())->record((int) Auth::id(), 'settings_updated');

        Session::flash('success', 'Settings updated.');
        $this->redirect('/admin/settings');
    }

    /**
     * A lightweight, dependency-free "backup" — exports every table's
     * current rows as plain INSERT statements. Not a substitute for the
     * real automated Cloud SQL backups planned for Phase 5 deployment,
     * but gives the Super Admin an on-demand, downloadable snapshot.
     */
    public function backup(): void
    {
        Auth::requireRole(['super_admin']);

        $pdo = Database::connection();
        $sql = "-- SmartEnroll manual backup — generated " . date('Y-m-d H:i:s') . "\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach (self::BACKUP_TABLES as $table) {
            $stmt = $pdo->query("SELECT * FROM `{$table}`");
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            if (empty($rows)) {
                continue;
            }

            $columns = array_keys($rows[0]);
            $columnList = '`' . implode('`, `', $columns) . '`';

            $sql .= "-- Table: {$table}\n";
            $sql .= "DELETE FROM `{$table}`;\n";
            foreach ($rows as $row) {
                $values = array_map(function ($value) use ($pdo) {
                    return $value === null ? 'NULL' : $pdo->quote((string) $value);
                }, $row);
                $sql .= "INSERT INTO `{$table}` ({$columnList}) VALUES (" . implode(', ', $values) . ");\n";
            }
            $sql .= "\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";

        (new AuditLog())->record((int) Auth::id(), 'backup_triggered');

        $filename = 'smartenroll_backup_' . date('Y-m-d_His') . '.sql';
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($sql));
        echo $sql;
        exit;
    }
}
