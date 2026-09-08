<div class="panel panel-wide">
    <h1>My Applications</h1>

    <?php if (!empty($success)): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <?php if (empty($applications)): ?>
        <p class="hero-subtitle">You haven't submitted any enrollment applications yet.</p>
        <a href="/enrollment/apply" class="btn-clay-primary">Start an Application</a>
    <?php else: ?>
        <div class="application-list">
            <?php foreach ($applications as $app): ?>
                <div class="application-card">
                    <div class="application-card-header">
                        <h3><?= htmlspecialchars($app['student_name']) ?></h3>
                        <span class="status-badge status-<?= htmlspecialchars($app['status']) ?>">
                            <?= htmlspecialchars(ucfirst($app['status'])) ?>
                        </span>
                    </div>
                    <p class="application-meta">
                        <?= htmlspecialchars($app['grade_level']) ?> ·
                        Submitted <?= htmlspecialchars(date('M j, Y', strtotime($app['created_at']))) ?>
                    </p>
                    <?php if ($app['status'] === 'approved' && !empty($app['section_name'])): ?>
                        <p class="application-detail">Assigned to <strong><?= htmlspecialchars($app['section_name']) ?></strong></p>
                    <?php endif; ?>
                    <?php if ($app['status'] === 'rejected' && !empty($app['reject_reason'])): ?>
                        <p class="application-detail application-detail-error"><?= htmlspecialchars($app['reject_reason']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <a href="/enrollment/apply" class="btn-clay-ghost" style="margin-top: 1.5rem; display: inline-block;">Submit Another Application</a>
    <?php endif; ?>
</div>
