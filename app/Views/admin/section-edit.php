<div class="panel panel-wide">
    <a href="/admin/sections" class="back-link">&larr; Back to sections</a>
    <h1>Edit Section</h1>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <p class="application-meta">
        Grade Level: <strong><?= htmlspecialchars($section['grade_level']) ?></strong>
        (not editable — create a new section to change grade level)
    </p>

    <form method="POST" action="/admin/sections/<?= (int) $section['id'] ?>">
        <label for="name">Section Name</label>
        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($section['name']) ?>">

        <label for="capacity">Capacity</label>
        <input type="number" id="capacity" name="capacity" min="<?= (int) $section['student_count'] ?>" required
               value="<?= (int) $section['capacity'] ?>">
        <p class="field-hint">
            Currently <?= (int) $section['student_count'] ?> student(s) assigned — capacity can't go below that.
        </p>

        <button type="submit">Save Changes</button>
    </form>
</div>
