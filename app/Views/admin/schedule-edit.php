<div class="panel panel-wide">
    <a href="/admin/schedule?section_id=<?= (int) $entry['section_id'] ?>" class="back-link">&larr; Back to schedule</a>
    <h1>Edit Schedule Entry</h1>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/admin/schedule/<?= (int) $entry['id'] ?>">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" required value="<?= htmlspecialchars($entry['subject']) ?>">

        <div class="form-row">
            <div>
                <label for="day_of_week">Day</label>
                <select id="day_of_week" name="day_of_week" required>
                    <?php foreach ($days as $day): ?>
                        <option value="<?= htmlspecialchars($day) ?>" <?= $entry['day_of_week'] === $day ? 'selected' : '' ?>>
                            <?= htmlspecialchars($day) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="room">Room</label>
                <input type="text" id="room" name="room" required value="<?= htmlspecialchars($entry['room']) ?>">
            </div>
        </div>

        <div class="form-row">
            <div>
                <label for="start_time">Start Time</label>
                <input type="time" id="start_time" name="start_time" required
                       value="<?= htmlspecialchars(substr($entry['start_time'], 0, 5)) ?>">
            </div>
            <div>
                <label for="end_time">End Time</label>
                <input type="time" id="end_time" name="end_time" required
                       value="<?= htmlspecialchars(substr($entry['end_time'], 0, 5)) ?>">
            </div>
        </div>

        <label for="teacher">Teacher</label>
        <input type="text" id="teacher" name="teacher" required value="<?= htmlspecialchars($entry['teacher']) ?>">

        <button type="submit">Save Changes</button>
    </form>
</div>
