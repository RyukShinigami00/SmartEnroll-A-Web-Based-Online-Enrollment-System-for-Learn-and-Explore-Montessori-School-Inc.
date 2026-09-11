<div class="panel panel-wide">
    <h1>Audit Log</h1>
    <p class="hero-subtitle" style="margin-bottom: 1.5rem;">
        A record of admin and super admin actions across the system.
    </p>

    <?php if (empty($logs)): ?>
        <p class="hero-subtitle">No activity recorded yet.</p>
    <?php else: ?>
        <div class="application-list">
            <?php foreach ($logs as $log): ?>
                <div class="application-card">
                    <div class="application-card-header">
                        <h3><?= htmlspecialchars(ucwords(str_replace('_', ' ', $log['action']))) ?></h3>
                        <span class="application-meta"><?= htmlspecialchars(date('M j, Y g:i A', strtotime($log['created_at']))) ?></span>
                    </div>
                    <p class="application-meta">
                        By <?= htmlspecialchars(trim(($log['first_name'] ?? '') . ' ' . ($log['last_name'] ?? '')) ?: 'System') ?>
                        <?php if (!empty($log['email'])): ?>(<?= htmlspecialchars($log['email']) ?>)<?php endif; ?>
                        <?php if (!empty($log['target_table'])): ?>
                            · Target: <?= htmlspecialchars($log['target_table']) ?> #<?= (int) $log['target_id'] ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($log['details'])): ?>
                        <p class="application-detail"><?= htmlspecialchars($log['details']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
