<div class="panel panel-wide">
    <h1>Student Records</h1>

    <?php if (!empty($success)): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <form method="GET" action="/admin/students" class="filter-form">
        <div>
            <label for="grade_level">Grade Level</label>
            <select id="grade_level" name="grade_level">
                <option value="">All Grades</option>
                <?php foreach ($gradeLevels as $level): ?>
                    <option value="<?= htmlspecialchars($level) ?>" <?= $activeGrade === $level ? 'selected' : '' ?>>
                        <?= htmlspecialchars($level) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="search">Search</label>
            <input type="text" id="search" name="search" placeholder="Student or parent name..."
                   value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="btn-clay-ghost-small filter-submit">Filter</button>
        <a href="/admin/students/export" class="btn-clay-ghost-small filter-submit">Export CSV</a>
    </form>

    <?php if (empty($students)): ?>
        <p class="hero-subtitle">No students match these filters.</p>
    <?php else: ?>
        <div class="application-list">
            <?php foreach ($students as $student): ?>
                <div class="application-card">
                    <div class="application-card-header">
                        <h3><?= htmlspecialchars($student['name']) ?></h3>
                        <a href="/admin/students/<?= (int) $student['id'] ?>/edit" class="btn-clay-ghost-small">Edit</a>
                    </div>
                    <p class="application-meta">
                        <?= htmlspecialchars($student['grade_level']) ?> ·
                        Section: <?= htmlspecialchars($student['section_name'] ?? '—') ?> ·
                        Parent: <?= htmlspecialchars($student['parent_name']) ?> ·
                        <?= htmlspecialchars($student['contact_number']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
