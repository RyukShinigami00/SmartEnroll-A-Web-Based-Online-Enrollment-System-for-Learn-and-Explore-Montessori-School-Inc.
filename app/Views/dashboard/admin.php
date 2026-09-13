<div class="panel">
    <span class="role-badge"><?= htmlspecialchars($role) ?></span>
    <h1>Welcome, <?= htmlspecialchars($name) ?></h1>
    <p>Review incoming enrollment applications or manage class sections.</p>
</div>

<?php
$capacity = (int) $sectionTotals['total_capacity'];
$enrolled = (int) $sectionTotals['total_enrolled'];
$utilization = $capacity > 0 ? round(($enrolled / $capacity) * 100) : 0;
$maxCount = max(1, $applicationCounts['pending'], $applicationCounts['approved'], $applicationCounts['rejected']);
?>

<div class="metric-cards">
    <div class="metric-card">
        <span class="metric-value"><?= $totalStudents ?></span>
        <span class="metric-label">Enrolled Students</span>
    </div>
    <div class="metric-card">
        <span class="metric-value"><?= $applicationCounts['pending'] ?></span>
        <span class="metric-label">Pending Applications</span>
    </div>
    <div class="metric-card">
        <span class="metric-value"><?= (int) $sectionTotals['total_sections'] ?></span>
        <span class="metric-label">Sections</span>
    </div>
    <div class="metric-card">
        <span class="metric-value"><?= $utilization ?>%</span>
        <span class="metric-label"><?= $enrolled ?>/<?= $capacity ?> Capacity Used</span>
    </div>
</div>

<div class="panel">
    <h2 style="margin-top:0;">Applications by Status</h2>
    <div class="bar-chart">
        <div class="bar-chart-row">
            <span class="bar-chart-label">Pending</span>
            <div class="bar-chart-track">
                <div class="bar-chart-fill bar-pending" style="width: <?= round(($applicationCounts['pending'] / $maxCount) * 100) ?>%;"></div>
            </div>
            <span class="bar-chart-count"><?= $applicationCounts['pending'] ?></span>
        </div>
        <div class="bar-chart-row">
            <span class="bar-chart-label">Approved</span>
            <div class="bar-chart-track">
                <div class="bar-chart-fill bar-approved" style="width: <?= round(($applicationCounts['approved'] / $maxCount) * 100) ?>%;"></div>
            </div>
            <span class="bar-chart-count"><?= $applicationCounts['approved'] ?></span>
        </div>
        <div class="bar-chart-row">
            <span class="bar-chart-label">Rejected</span>
            <div class="bar-chart-track">
                <div class="bar-chart-fill bar-rejected" style="width: <?= round(($applicationCounts['rejected'] / $maxCount) * 100) ?>%;"></div>
            </div>
            <span class="bar-chart-count"><?= $applicationCounts['rejected'] ?></span>
        </div>
    </div>
</div>

<div class="panel">
    <h2 style="margin-top:0;">Quick Actions</h2>
    <div class="dashboard-actions">
        <a href="/admin/applications" class="btn-clay-primary">Review Applications</a>
        <a href="/admin/students" class="btn-clay-ghost">Student Records</a>
        <a href="/admin/sections" class="btn-clay-ghost">Manage Sections</a>
        <a href="/admin/schedule" class="btn-clay-ghost">Manage Schedules</a>
        <a href="/admin/reports" class="btn-clay-ghost">Reports</a>
        <?php if ($role === 'super_admin'): ?>
            <a href="/admin/audit-log" class="btn-clay-ghost">View Audit Log</a>
            <a href="/admin/users" class="btn-clay-ghost">Manage Admin Accounts</a>
            <a href="/admin/settings" class="btn-clay-ghost">System Settings</a>
        <?php endif; ?>
    </div>
</div>
