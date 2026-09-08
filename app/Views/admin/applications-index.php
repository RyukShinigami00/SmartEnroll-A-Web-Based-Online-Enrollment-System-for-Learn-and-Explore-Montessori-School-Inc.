<div class="panel panel-wide">
    <h1>Enrollment Applications</h1>

    <?php if (!empty($success)): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="filter-tabs">
        <a href="/admin/applications" class="filter-tab <?= $activeStatus === 'all' ? 'filter-tab-active' : '' ?>">All</a>
        <a href="/admin/applications?status=pending" class="filter-tab <?= $activeStatus === 'pending' ? 'filter-tab-active' : '' ?>">Pending</a>
        <a href="/admin/applications?status=approved" class="filter-tab <?= $activeStatus === 'approved' ? 'filter-tab-active' : '' ?>">Approved</a>
        <a href="/admin/applications?status=rejected" class="filter-tab <?= $activeStatus === 'rejected' ? 'filter-tab-active' : '' ?>">Rejected</a>
    </div>

    <?php if (empty($applications)): ?>
        <p class="hero-subtitle">No applications here yet.</p>
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
