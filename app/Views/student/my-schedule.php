<div class="panel panel-wide">
    <h1>My Schedule</h1>

    <?php if (empty($schedules)): ?>
        <p class="hero-subtitle">Schedule will appear after approval.</p>
        <a href="/my-applications" class="btn-clay-ghost">View My Applications</a>
    <?php else: ?>
        <?php foreach ($schedules as $item): ?>
            <?php $student = $item['student']; $entries = $item['entries']; ?>
            <div class="schedule-block">
                <div class="application-card-header">
                    <h2><?= htmlspecialchars($student['name']) ?></h2>
                    <a href="/my-schedule/<?= (int) $student['id'] ?>/pdf" class="btn-clay-ghost-small">Download PDF</a>
                </div>
                <p class="application-meta">Section: <strong><?= htmlspecialchars($student['section_name'] ?? '—') ?></strong></p>

                <?php if (empty($entries)): ?>
                    <p class="hero-subtitle">No schedule entries have been added for this section yet.</p>
                <?php else: ?>
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Subject</th>
                                <th>Room</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entries as $entry): ?>
                                <tr>
                                    <td><?= htmlspecialchars($entry['day_of_week']) ?></td>
                                    <td>
                                        <?= htmlspecialchars(date('g:i A', strtotime($entry['start_time']))) ?> –
                                        <?= htmlspecialchars(date('g:i A', strtotime($entry['end_time']))) ?>
                                    </td>
                                    <td><?= htmlspecialchars($entry['subject']) ?></td>
                                    <td><?= htmlspecialchars($entry['room']) ?></td>
                                    <td><?= htmlspecialchars($entry['teacher']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php
                    // Group entries by day for the compact weekly grid view below.
                    $byDay = ['Mon' => [], 'Tue' => [], 'Wed' => [], 'Thu' => [], 'Fri' => [], 'Sat' => []];
                    foreach ($entries as $entry) {
                        $byDay[$entry['day_of_week']][] = $entry;
                    }
                    ?>
                    <div class="week-grid">
                        <?php foreach ($byDay as $day => $dayEntries): ?>
                            <div class="week-day">
                                <div class="week-day-label"><?= htmlspecialchars($day) ?></div>
                                <?php foreach ($dayEntries as $entry): ?>
                                    <div class="week-entry">
                                        <strong><?= htmlspecialchars($entry['subject']) ?></strong>
                                        <span><?= htmlspecialchars(date('g:i A', strtotime($entry['start_time']))) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
