<div class="panel panel-wide reports-panel">
    <div class="reports-header">
        <div>
            <h1>Reports</h1>
            <p class="application-meta">Generated <?= htmlspecialchars($generatedAt) ?></p>
        </div>
        <button onclick="window.print()" class="btn-clay-ghost-small no-print">Print Report</button>
    </div>

    <h2>Enrollment Summary</h2>
    <div class="metric-cards">
        <div class="metric-card">
            <span class="metric-value"><?= $applicationCounts['pending'] + $applicationCounts['approved'] + $applicationCounts['rejected'] ?></span>
            <span class="metric-label">Total Applications</span>
        </div>
        <div class="metric-card">
            <span class="metric-value"><?= $applicationCounts['pending'] ?></span>
            <span class="metric-label">Pending</span>
        </div>
        <div class="metric-card">
            <span class="metric-value"><?= $applicationCounts['approved'] ?></span>
            <span class="metric-label">Approved</span>
        </div>
        <div class="metric-card">
            <span class="metric-value"><?= $applicationCounts['rejected'] ?></span>
            <span class="metric-label">Rejected</span>
        </div>
    </div>

    <h2 style="margin-top:2rem;">Students per Section</h2>
    <table class="schedule-table">
        <thead>
            <tr>
                <th>Section</th>
                <th>Grade Level</th>
                <th>Enrolled</th>
                <th>Capacity</th>
                <th>Utilization</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sections as $section): ?>
                <?php
                    $count = (int) $section['student_count'];
                    $cap = (int) $section['capacity'];
                    $pct = $cap > 0 ? round(($count / $cap) * 100) : 0;
                ?>
                <tr>
                    <td><?= htmlspecialchars($section['name']) ?></td>
                    <td><?= htmlspecialchars($section['grade_level']) ?></td>
                    <td><?= $count ?></td>
                    <td><?= $cap ?></td>
                    <td><?= $pct ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p class="application-meta">Total enrolled across all sections: <strong><?= $totalStudents ?></strong></p>

    <h2 style="margin-top:2rem;">Applications — Last 6 Months</h2>
    <?php if (empty($monthlyTrend)): ?>
        <p class="hero-subtitle">No applications submitted in this period.</p>
    <?php else: ?>
        <table class="schedule-table">
            <thead>
                <tr><th>Month</th><th>Applications Submitted</th></tr>
            </thead>
            <tbody>
                <?php foreach ($monthlyTrend as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('F Y', strtotime($row['month'] . '-01'))) ?></td>
                        <td><?= (int) $row['total'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
