<div class="panel panel-wide">
    <h1>Enrollment Applications</h1>

    <?php if (!empty($success)): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php
    // Helper to build a status-tab URL that preserves the current grade/search filters.
    $tabUrl = function (string $status) use ($activeGrade, $search) {
        $params = [];
        if ($status !== 'all') $params['status'] = $status;
        if ($activeGrade !== '') $params['grade_level'] = $activeGrade;
        if ($search !== '') $params['search'] = $search;
        return '/admin/applications' . ($params ? '?' . http_build_query($params) : '');
    };
    ?>

    <div class="filter-tabs">
        <a href="<?= htmlspecialchars($tabUrl('all')) ?>" class="filter-tab <?= $activeStatus === 'all' ? 'filter-tab-active' : '' ?>">All</a>
        <a href="<?= htmlspecialchars($tabUrl('pending')) ?>" class="filter-tab <?= $activeStatus === 'pending' ? 'filter-tab-active' : '' ?>">Pending</a>
        <a href="<?= htmlspecialchars($tabUrl('approved')) ?>" class="filter-tab <?= $activeStatus === 'approved' ? 'filter-tab-active' : '' ?>">Approved</a>
        <a href="<?= htmlspecialchars($tabUrl('rejected')) ?>" class="filter-tab <?= $activeStatus === 'rejected' ? 'filter-tab-active' : '' ?>">Rejected</a>
    </div>

    <form method="GET" action="/admin/applications" class="filter-form">
        <?php if ($activeStatus !== 'all'): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($activeStatus) ?>">
        <?php endif; ?>

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
    </form>

    <?php if (empty($applications)): ?>
        <p class="hero-subtitle">No applications match these filters.</p>
    <?php else: ?>
        <div class="application-list">
            <?php foreach ($applications as $app): ?>
                <a href="/admin/applications/<?= (int) $app['id'] ?>" class="application-card application-card-link">
                    <div class="application-card-header">
                        <h3><?= htmlspecialchars($app['student_name']) ?></h3>
                        <span class="status-badge status-<?= htmlspecialchars($app['status']) ?>">
                            <?= htmlspecialchars(ucfirst($app['status'])) ?>
                        </span>
                    </div>
                    <p class="application-meta">
                        <?= htmlspecialchars($app['grade_level']) ?> ·
                        Parent: <?= htmlspecialchars($app['parent_name']) ?> ·
                        Submitted <?= htmlspecialchars(date('M j, Y', strtotime($app['created_at']))) ?>
                    </p>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
