<div class="panel">
    <span class="role-badge"><?= htmlspecialchars($role) ?></span>
    <h1>Welcome, <?= htmlspecialchars($name) ?></h1>
    <p>Review incoming enrollment applications or manage class sections.</p>
    <div class="dashboard-actions">
        <a href="/admin/applications" class="btn-clay-primary">Review Applications</a>
        <a href="/admin/sections" class="btn-clay-ghost">Manage Sections</a>
        <a href="/admin/schedule" class="btn-clay-ghost">Manage Schedules</a>
        <?php if ($role === 'super_admin'): ?>
            <a href="/admin/audit-log" class="btn-clay-ghost">View Audit Log</a>
            <a href="/admin/users" class="btn-clay-ghost">Manage Admin Accounts</a>
        <?php endif; ?>
    </div>
</div>
