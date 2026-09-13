<div class="panel panel-wide">
    <h1>System Settings</h1>

    <?php if (!empty($success)): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="/admin/settings">
        <label for="school_name">School Name</label>
        <input type="text" id="school_name" name="school_name" required
               value="<?= htmlspecialchars($settings['school_name'] ?? '') ?>">

        <label for="school_email">Contact Email</label>
        <input type="email" id="school_email" name="school_email" required
               value="<?= htmlspecialchars($settings['school_email'] ?? '') ?>">

        <label for="school_contact_number">Contact Number</label>
        <input type="text" id="school_contact_number" name="school_contact_number"
               value="<?= htmlspecialchars($settings['school_contact_number'] ?? '') ?>">

        <button type="submit">Save Settings</button>
    </form>

    <h2 style="margin-top:2.5rem;">Database Backup</h2>
    <p class="hero-subtitle">
        Download an on-demand snapshot of all current data as a SQL file.
        This is separate from the automated daily backups configured on GCP Cloud SQL in production.
    </p>
    <a href="/admin/settings/backup" class="btn-clay-ghost">Download Backup (.sql)</a>
</div>
