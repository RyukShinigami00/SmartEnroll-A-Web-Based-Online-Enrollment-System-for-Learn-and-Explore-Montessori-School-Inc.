<div class="auth-shell">
    <div class="auth-panel-form">
        <span class="auth-eyebrow">SmartEnroll · LEMS</span>
        <h1>Create Your Account</h1>
        <p class="auth-subtitle">Join SmartEnroll to submit and track enrollment online.</p>

        <?php if (!empty($error)): ?>
            <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" action="/register">
            <div class="form-row">
                <div>
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required autofocus
                           value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
                </div>
                <div>
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required
                           value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
                </div>
            </div>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($old['email'] ?? '') ?>">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" minlength="8" required>
            <p class="field-hint">
                At least 8 characters, with 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.
            </p>

            <label for="password_confirm">Confirm Password</label>
            <input type="password" id="password_confirm" name="password_confirm" minlength="8" required>

            <button type="submit">Register</button>
        </form>

        <p class="auth-footnote">Already have an account? <a href="/login">Log in</a></p>
    </div>

    <div class="auth-panel-art">
        <?php include __DIR__ . '/../partials/clay-illustration.php'; ?>
        <p class="auth-caption">Every child, a personalized path.</p>
    </div>
</div>
