<div class="panel panel-wide">
    <h1>Schedule Management</h1>

    <?php if (!empty($success)): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="GET" action="/admin/schedule" class="filter-form">
        <div>
            <label for="section_id">Section</label>
            <select id="section_id" name="section_id" onchange="this.form.submit()">
                <option value="0">Select a section...</option>
                <?php foreach ($sections as $section): ?>
                    <option value="<?= (int) $section['id'] ?>" <?= $selectedSectionId === (int) $section['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($section['grade_level']) ?> — <?= htmlspecialchars($section['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if ($selectedSectionId > 0): ?>
        <?php if (empty($entries)): ?>
            <p class="hero-subtitle">No schedule entries yet for this section.</p>
        <?php else: ?>
            <div class="application-list">
                <?php foreach ($entries as $entry): ?>
                    <div class="application-card">
                        <div class="application-card-header">
                            <h3><?= htmlspecialchars($entry['subject']) ?></h3>
                            <span class="status-badge status-approved"><?= htmlspecialchars($entry['day_of_week']) ?></span>
                        </div>
                        <p class="application-meta">
                            <?= htmlspecialchars(date('g:i A', strtotime($entry['start_time']))) ?> –
                            <?= htmlspecialchars(date('g:i A', strtotime($entry['end_time']))) ?> ·
                            Room <?= htmlspecialchars($entry['room']) ?> ·
                            <?= htmlspecialchars($entry['teacher']) ?>
                        </p>
                        <div class="section-actions">
                            <a href="/admin/schedule/<?= (int) $entry['id'] ?>/edit" class="btn-clay-ghost-small">Edit</a>
                            <form method="POST" action="/admin/schedule/<?= (int) $entry['id'] ?>/delete"
                                  onsubmit="return confirm('Remove &quot;<?= htmlspecialchars(addslashes($entry['subject'])) ?>&quot; from the schedule?');">
                                <button type="submit" class="btn-clay-ghost-small btn-delete">Delete</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h2 style="margin-top:2rem;">Add Schedule Entry</h2>
        <form method="POST" action="/admin/schedule">
            <input type="hidden" name="section_id" value="<?= $selectedSectionId ?>">

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" placeholder="e.g. Practical Life" required>

            <div class="form-row">
                <div>
                    <label for="day_of_week">Day</label>
                    <select id="day_of_week" name="day_of_week" required>
                        <?php foreach ($days as $day): ?>
                            <option value="<?= htmlspecialchars($day) ?>"><?= htmlspecialchars($day) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="room">Room</label>
                    <input type="text" id="room" name="room" placeholder="e.g. Room 2" required>
                </div>
            </div>

            <div class="form-row">
                <div>
                    <label for="start_time">Start Time</label>
                    <input type="time" id="start_time" name="start_time" required>
                </div>
                <div>
                    <label for="end_time">End Time</label>
                    <input type="time" id="end_time" name="end_time" required>
                </div>
            </div>

            <label for="teacher">Teacher</label>
            <input type="text" id="teacher" name="teacher" placeholder="e.g. Ms. Santos" required>

            <button type="submit">Add to Schedule</button>
        </form>
    <?php else: ?>
        <p class="hero-subtitle">Select a section above to view and manage its schedule.</p>
    <?php endif; ?>
</div>
