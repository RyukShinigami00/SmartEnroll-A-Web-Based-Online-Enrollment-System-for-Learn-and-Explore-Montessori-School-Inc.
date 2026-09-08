<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Middleware\Auth;
use App\Models\AuditLog;
use App\Models\Section;

class SectionController extends Controller
{
    private Section $sectionModel;

    private const GRADE_LEVELS = ['Toddler', 'Primary'];

    public function __construct()
    {
        $this->sectionModel = new Section();
    }

    public function index(): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $this->view('admin/sections', [
            'sections'    => $this->sectionModel->allWithCounts(),
            'gradeLevels' => self::GRADE_LEVELS,
            'error'       => Session::flash('error'),
            'success'     => Session::flash('success'),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $gradeLevel = trim((string) $this->input('grade_level'));
        $name = trim((string) $this->input('name'));
        $capacity = (int) $this->input('capacity');

        if ($gradeLevel === '' || $name === '' || $capacity <= 0) {
            Session::flash('error', 'Please fill in all fields with a valid capacity.');
            $this->redirect('/admin/sections');
        }

        if (!in_array($gradeLevel, self::GRADE_LEVELS, true)) {
            Session::flash('error', 'Please select a valid grade level.');
            $this->redirect('/admin/sections');
        }

        try {
            $sectionId = $this->sectionModel->createSection($gradeLevel, $name, $capacity);
        } catch (\PDOException $e) {
            if ((int) $e->getCode() === 23000) {
                Session::flash('error', "A {$gradeLevel} section named \"{$name}\" already exists.");
                $this->redirect('/admin/sections');
            }
            throw $e;
        }

        (new AuditLog())->record((int) Auth::id(), 'section_created', 'sections', $sectionId);

        Session::flash('success', "Section \"{$name}\" created.");
        $this->redirect('/admin/sections');
    }
}
