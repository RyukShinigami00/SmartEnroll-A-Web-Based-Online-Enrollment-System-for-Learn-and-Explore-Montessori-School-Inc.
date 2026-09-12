<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\Session;
use App\Middleware\Auth;
use App\Models\AuditLog;
use App\Models\ScheduleEntry;
use App\Models\Section;

class ScheduleController extends Controller
{
    private ScheduleEntry $scheduleModel;
    private Section $sectionModel;

    private const DAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

    public function __construct()
    {
        $this->scheduleModel = new ScheduleEntry();
        $this->sectionModel = new Section();
    }

    public function index(): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $sections = $this->sectionModel->allWithCounts();
        $selectedSectionId = (int) $this->input('section_id', 0);

        $entries = $selectedSectionId > 0 ? $this->scheduleModel->allBySection($selectedSectionId) : [];

        $this->view('admin/schedule', [
            'sections'          => $sections,
            'selectedSectionId' => $selectedSectionId,
            'entries'           => $entries,
            'days'              => self::DAYS,
            'error'             => Session::flash('error'),
            'success'           => Session::flash('success'),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $sectionId = (int) $this->input('section_id');
        [$valid, $data, $error] = $this->validateEntry($sectionId);

        if (!$valid) {
            Session::flash('error', $error);
            $this->redirect("/admin/schedule?section_id={$sectionId}");
        }

        $conflict = $this->scheduleModel->findConflict($sectionId, $data['day_of_week'], $data['start_time'], $data['end_time']);
        if ($conflict) {
            Session::flash('error', "Conflicts with an existing entry: {$conflict['subject']} on {$conflict['day_of_week']} ({$conflict['start_time']}–{$conflict['end_time']}).");
            $this->redirect("/admin/schedule?section_id={$sectionId}");
        }

        $data['section_id'] = $sectionId;
        $entryId = $this->scheduleModel->createEntry($data);

        (new AuditLog())->record((int) Auth::id(), 'schedule_entry_created', 'schedule_entries', $entryId, "{$data['subject']} — {$data['day_of_week']} {$data['start_time']}-{$data['end_time']}");

        Session::flash('success', "Added \"{$data['subject']}\" to the schedule.");
        $this->redirect("/admin/schedule?section_id={$sectionId}");
    }

    public function edit(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $entry = $this->scheduleModel->find((int) $id);

        if (!$entry) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $this->view('admin/schedule-edit', [
            'entry' => $entry,
            'days'  => self::DAYS,
            'error' => Session::flash('error'),
        ]);
    }

    public function update(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $entryId = (int) $id;
        $entry = $this->scheduleModel->find($entryId);

        if (!$entry) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $sectionId = (int) $entry['section_id'];
        [$valid, $data, $error] = $this->validateEntry($sectionId);

        if (!$valid) {
            Session::flash('error', $error);
            $this->redirect("/admin/schedule/{$entryId}/edit");
        }

        $conflict = $this->scheduleModel->findConflict($sectionId, $data['day_of_week'], $data['start_time'], $data['end_time'], $entryId);
        if ($conflict) {
            Session::flash('error', "Conflicts with an existing entry: {$conflict['subject']} on {$conflict['day_of_week']} ({$conflict['start_time']}–{$conflict['end_time']}).");
            $this->redirect("/admin/schedule/{$entryId}/edit");
        }

        $this->scheduleModel->updateEntry($entryId, $data);

        (new AuditLog())->record((int) Auth::id(), 'schedule_entry_updated', 'schedule_entries', $entryId, "{$data['subject']} — {$data['day_of_week']} {$data['start_time']}-{$data['end_time']}");

        Session::flash('success', "Updated \"{$data['subject']}\".");
        $this->redirect("/admin/schedule?section_id={$sectionId}");
    }

    public function destroy(string $id): void
    {
        Auth::requireRole(['admin', 'super_admin']);

        $entryId = (int) $id;
        $entry = $this->scheduleModel->find($entryId);

        if (!$entry) {
            http_response_code(404);
            require __DIR__ . '/../Views/errors/404.php';
            return;
        }

        $sectionId = (int) $entry['section_id'];
        $this->scheduleModel->delete($entryId);

        (new AuditLog())->record((int) Auth::id(), 'schedule_entry_deleted', 'schedule_entries', $entryId, $entry['subject']);

        Session::flash('success', "Removed \"{$entry['subject']}\" from the schedule.");
        $this->redirect("/admin/schedule?section_id={$sectionId}");
    }

    /**
     * @return array{0: bool, 1: array, 2: string} [isValid, cleanedData, errorMessage]
     */
    private function validateEntry(int $sectionId): array
    {
        $subject = trim((string) $this->input('subject'));
        $dayOfWeek = trim((string) $this->input('day_of_week'));
        $startTime = trim((string) $this->input('start_time'));
        $endTime = trim((string) $this->input('end_time'));
        $room = trim((string) $this->input('room'));
        $teacher = trim((string) $this->input('teacher'));

        $data = [
            'subject'     => $subject,
            'day_of_week' => $dayOfWeek,
            'start_time'  => $startTime,
            'end_time'    => $endTime,
            'room'        => $room,
            'teacher'     => $teacher,
        ];

        if ($sectionId <= 0) {
            return [false, $data, 'Please select a section.'];
        }

        if ($subject === '' || $dayOfWeek === '' || $startTime === '' || $endTime === '' || $room === '' || $teacher === '') {
            return [false, $data, 'All fields are required.'];
        }

        if (!in_array($dayOfWeek, self::DAYS, true)) {
            return [false, $data, 'Please select a valid day.'];
        }

        if (strtotime($startTime) === false || strtotime($endTime) === false) {
            return [false, $data, 'Please enter valid start and end times.'];
        }

        if (strtotime($startTime) >= strtotime($endTime)) {
            return [false, $data, 'End time must be after start time.'];
        }

        return [true, $data, ''];
    }
}
