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

    public function edit(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $section = $this->sectionModel->findWithCount((int) $id);

        if (!$section) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $this->view('admin/section-edit', [
            'section' => $section,
            'error'   => Session::flash('error'),
        ]);
    }

    public function update(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $sectionId = (int) $id;
        $section = $this->sectionModel->findWithCount($sectionId);

        if (!$section) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $name = trim((string) $this->input('name'));
        $capacity = (int) $this->input('capacity');

        if ($name === '' || $capacity <= 0) {
            Session::flash('error', 'Please provide a name and a valid capacity.');
            $this->redirect("/admin/sections/{$sectionId}/edit");
        }

        $currentCount = (int) $section['student_count'];
        if ($capacity < $currentCount) {
            Session::flash('error', "Capacity can't be less than the {$currentCount} student(s) already assigned to this section.");
            $this->redirect("/admin/sections/{$sectionId}/edit");
        }

        try {
            $this->sectionModel->updateSection($sectionId, $name, $capacity);
        } catch (\PDOException $e) {
            if ((int) $e->getCode() === 23000) {
                Session::flash('error', "A {$section['grade_level']} section named \"{$name}\" already exists.");
                $this->redirect("/admin/sections/{$sectionId}/edit");
            }
            throw $e;
        }

        (new AuditLog())->record((int) Auth::id(), 'section_updated', 'sections', $sectionId, "Renamed to \"{$name}\", capacity {$capacity}");

        Session::flash('success', "Section \"{$name}\" updated.");
        $this->redirect('/admin/sections');
    }

    public function destroy(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $sectionId = (int) $id;
        $section = $this->sectionModel->findWithCount($sectionId);

        if (!$section) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        if ((int) $section['student_count'] > 0) {
            Session::flash('error', "\"{$section['name']}\" still has {$section['student_count']} student(s) assigned and can't be deleted. Reassign them first.");
            $this->redirect('/admin/sections');
        }

        $this->sectionModel->deleteSection($sectionId);
        (new AuditLog())->record((int) Auth::id(), 'section_deleted', 'sections', $sectionId, $section['name']);

        Session::flash('success', "Section \"{$section['name']}\" deleted.");
        $this->redirect('/admin/sections');
    }
}
