<div class="panel panel-wide">
    <a href="/admin/students" class="back-link">&larr; Back to student records</a>
    <h1><?= htmlspecialchars($student['name']) ?></h1>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="detail-grid">
        <div><span class="detail-label">Date of Birth</span><?= htmlspecialchars(date('M j, Y', strtotime($student['date_of_birth']))) ?></div>
        <div><span class="detail-label">Grade Level</span><?= htmlspecialchars($student['grade_level']) ?></div>
        <div><span class="detail-label">Parent / Guardian</span><?= htmlspecialchars($student['parent_name']) ?></div>
        <div><span class="detail-label">Enrolled Since</span><?= htmlspecialchars(date('M j, Y', strtotime($student['created_at']))) ?></div>
    </div>

    <form method="POST" action="/admin/students/<?= (int) $student['id'] ?>">
        <label for="contact_number">Contact Number</label>
        <input type="tel" id="contact_number" name="contact_number" required
               value="<?= htmlspecialchars($student['contact_number']) ?>">

        <label for="address">Home Address</label>
        <input type="text" id="address" name="address" required
               value="<?= htmlspecialchars($student['address']) ?>">

        <label for="section_id">Section</label>
        <select id="section_id" name="section_id" required>
            <?php foreach ($sections as $section): ?>
                <?php
                    $isCurrent = (int) $section['id'] === (int) $student['section_id'];
                    $full = (int) $section['student_count'] >= (int) $section['capacity'] && !$isCurrent;
                ?>
                <option value="<?= (int) $section['id'] ?>" <?= $isCurrent ? 'selected' : '' ?> <?= $full ? 'disabled' : '' ?>>
                    <?= htmlspecialchars($section['name']) ?>
                    (<?= (int) $section['student_count'] ?>/<?= (int) $section['capacity'] ?><?= $full ? ' — full' : '' ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Save Changes</button>
    </form>
</div>
