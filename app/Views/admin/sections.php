<div class="panel panel-wide">
    <h1>Sections</h1>

    <?php if (!empty($success)): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="application-list">
        <?php foreach ($sections as $section): ?>
            <?php $full = (int) $section['student_count'] >= (int) $section['capacity']; ?>
            <div class="application-card">
                <div class="application-card-header">
                    <h3><?= htmlspecialchars($section['name']) ?></h3>
                    <span class="status-badge <?= $full ? 'status-rejected' : 'status-approved' ?>">
                        <?= (int) $section['student_count'] ?>/<?= (int) $section['capacity'] ?>
                    </span>
                </div>
                <p class="application-meta"><?= htmlspecialchars($section['grade_level']) ?></p>
                <div class="section-actions">
                    <a href="/admin/sections/<?= (int) $section['id'] ?>/edit" class="btn-clay-ghost-small">Edit</a>
                    <form method="POST" action="/admin/sections/<?= (int) $section['id'] ?>/delete"
                          onsubmit="return confirm('Delete &quot;<?= htmlspecialchars(addslashes($section['name'])) ?>&quot;? This can\'t be undone.');">
                        <button type="submit" class="btn-clay-ghost-small btn-delete"
                            <?= (int) $section['student_count'] > 0 ? 'disabled title="Cannot delete a section with students assigned"' : '' ?>>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 style="margin-top:2rem;">Add a Section</h2>
    <form method="POST" action="/admin/sections">
        <label for="grade_level">Grade Level</label>
        <select id="grade_level" name="grade_level" required>
            <?php foreach ($gradeLevels as $level): ?>
                <option value="<?= htmlspecialchars($level) ?>"><?= htmlspecialchars($level) ?></option>
            <?php endforeach; ?>
        </select>

        <label for="name">Section Name</label>
        <input type="text" id="name" name="name" placeholder="e.g. Primary C" required>

        <label for="capacity">Capacity</label>
        <input type="number" id="capacity" name="capacity" min="1" value="20" required>

        <button type="submit">Create Section</button>
    </form>
</div>
