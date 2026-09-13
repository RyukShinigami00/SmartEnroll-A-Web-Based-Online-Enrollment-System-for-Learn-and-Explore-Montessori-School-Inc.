<?php

namespace App\Helpers;

require_once __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';

class SchedulePdf
{
    /**
     * Renders a student's schedule as a PDF and returns the raw PDF
     * bytes (ready to stream as a download — see StudentScheduleController).
     */
    public static function generate(string $studentName, string $sectionName, array $entries): string
    {
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator('SmartEnroll');
        $pdf->SetAuthor('Learn and Explore Montessori School');
        $pdf->SetTitle($studentName . ' - Class Schedule');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        $name = htmlspecialchars($studentName);
        $section = htmlspecialchars($sectionName);

        $html = <<<HTML
        <h1 style="color:#55704F; font-size: 18pt;">SmartEnroll — Class Schedule</h1>
        <p style="font-size: 11pt;">
            <strong>Student:</strong> {$name}<br>
            <strong>Section:</strong> {$section}
        </p>
        HTML;

        if (empty($entries)) {
            $html .= '<p style="font-size: 11pt; color: #8A8272;">No schedule entries have been added for this section yet.</p>';
        } else {
            $html .= '<table border="1" cellpadding="6" style="font-size: 10pt;">
                <thead>
                    <tr style="background-color:#DCE6D8; font-weight:bold;">
                        <th width="15%">Day</th>
                        <th width="25%">Time</th>
                        <th width="25%">Subject</th>
                        <th width="15%">Room</th>
                        <th width="20%">Teacher</th>
                    </tr>
                </thead>
                <tbody>';

            foreach ($entries as $entry) {
                $day = htmlspecialchars($entry['day_of_week']);
                $time = htmlspecialchars(date('g:i A', strtotime($entry['start_time'])) . ' – ' . date('g:i A', strtotime($entry['end_time'])));
                $subject = htmlspecialchars($entry['subject']);
                $room = htmlspecialchars($entry['room']);
                $teacher = htmlspecialchars($entry['teacher']);

                $html .= "<tr><td>{$day}</td><td>{$time}</td><td>{$subject}</td><td>{$room}</td><td>{$teacher}</td></tr>";
            }

            $html .= '</tbody></table>';
        }

        $pdf->writeHTML($html, true, false, true, false, '');

        return $pdf->Output('schedule.pdf', 'S');
    }
}
