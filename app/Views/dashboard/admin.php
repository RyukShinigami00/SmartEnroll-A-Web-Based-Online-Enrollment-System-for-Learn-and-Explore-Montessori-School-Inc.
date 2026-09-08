<div class="panel">
    <span class="role-badge"><?= htmlspecialchars($role) ?></span>
    <h1>Welcome, <?= htmlspecialchars($name) ?></h1>
    <p>Review incoming enrollment applications or manage class sections.</p>
    <div class="dashboard-actions">
        <a href="/admin/applications" class="btn-clay-primary">Review Applications</a>
        <a href="/admin/sections" class="btn-clay-ghost">Manage Sections</a>
    </div>
</div>
