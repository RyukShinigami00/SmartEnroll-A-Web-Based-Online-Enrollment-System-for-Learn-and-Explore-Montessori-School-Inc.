<div class="panel panel-wide">
    <a href="/admin/applications" class="back-link">&larr; Back to all applications</a>
    <h1><?= htmlspecialchars($application['student_name']) ?></h1>
    <span class="status-badge status-<?= htmlspecialchars($application['status']) ?>">
        <?= htmlspecialchars(ucfirst($application['status'])) ?>
    </span>

    <?php if (!empty($error)): ?>
        <p class="alert alert-error" style="margin-top:1rem;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="detail-grid">
        <div><span class="detail-label">Date of Birth</span><?= htmlspecialchars(date('M j, Y', strtotime($application['date_of_birth']))) ?></div>
        <div><span class="detail-label">Grade Level</span><?= htmlspecialchars($application['grade_level']) ?></div>
        <div><span class="detail-label">Parent / Guardian</span><?= htmlspecialchars($application['parent_name']) ?></div>
        <div><span class="detail-label">Contact Number</span><?= htmlspecialchars($application['contact_number']) ?></div>
        <div><span class="detail-label">Address</span><?= htmlspecialchars($application['address']) ?></div>
        <div><span class="detail-label">Account Email</span><?= htmlspecialchars($application['applicant_email'] ?? '—') ?></div>
        <div><span class="detail-label">Submitted</span><?= htmlspecialchars(date('M j, Y g:i A', strtotime($application['created_at']))) ?></div>
    </div>

    <?php if ($application['status'] === 'pending'): ?>
        <div class="review-actions">
            <form method="POST" action="/admin/applications/<?= (int) $application['id'] ?>/approve" class="review-form">
                <label for="section_id">Assign to Section</label>
                <select id="section_id" name="section_id" required>
                    <option value="">Select a section...</option>
                    <?php foreach ($sections as $section): ?>
                        <?php $full = (int) $section['student_count'] >= (int) $section['capacity']; ?>
                        <option value="<?= (int) $section['id'] ?>" <?= $full ? 'disabled' : '' ?>>
                            <?= htmlspecialchars($section['name']) ?>
                            (<?= (int) $section['student_count'] ?>/<?= (int) $section['capacity'] ?><?= $full ? ' — full' : '' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-clay-primary">Approve</button>
            </form>

            <form method="POST" action="/admin/applications/<?= (int) $application['id'] ?>/reject" class="review-form">
                <label for="reject_reason">Rejection Reason</label>
                <input type="text" id="reject_reason" name="reject_reason" placeholder="e.g. Section full for this school year" required>
                <button type="submit" class="btn-clay-ghost">Reject</button>
            </form>
        </div>
    <?php elseif ($application['status'] === 'rejected' && !empty($application['reject_reason'])): ?>
        <p class="application-detail application-detail-error" style="margin-top:1.5rem;">
            Reason: <?= htmlspecialchars($application['reject_reason']) ?>
        </p>
    <?php endif; ?>
</div>
