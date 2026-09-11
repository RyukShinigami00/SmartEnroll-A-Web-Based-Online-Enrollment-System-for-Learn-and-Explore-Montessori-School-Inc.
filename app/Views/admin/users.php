<div class="panel panel-wide">
    <h1>Manage Admin Accounts</h1>

    <?php if (!empty($success)): ?>
        <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <div class="application-list">
        <?php foreach ($admins as $admin): ?>
            <div class="application-card">
                <div class="application-card-header">
                    <h3><?= htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) ?></h3>
                    <span class="status-badge <?= (int) $admin['is_active'] === 1 ? 'status-approved' : 'status-rejected' ?>">
                        <?= (int) $admin['is_active'] === 1 ? 'Active' : 'Deactivated' ?>
                    </span>
                </div>
                <p class="application-meta">
                    <?= htmlspecialchars($admin['email']) ?> · <?= htmlspecialchars(ucwords(str_replace('_', ' ', $admin['role']))) ?>
                </p>
                <form method="POST" action="/admin/users/<?= (int) $admin['id'] ?>/toggle" style="margin-top: 0.75rem;">
                    <button type="submit" class="btn-clay-ghost-small">
                        <?= (int) $admin['is_active'] === 1 ? 'Deactivate' : 'Reactivate' ?>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <h2 style="margin-top:2rem;">Create Admin Account</h2>
    <form method="POST" action="/admin/users">
        <div class="form-row">
            <div>
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div>
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
        </div>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Temporary Password</label>
        <input type="password" id="password" name="password" minlength="8" required>
        <p class="field-hint">At least 8 characters, with 1 uppercase, 1 lowercase, 1 number, and 1 special character.</p>

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="admin">Admin</option>
            <option value="super_admin">Super Admin</option>
        </select>

        <button type="submit">Create Account</button>
    </form>
</div>
