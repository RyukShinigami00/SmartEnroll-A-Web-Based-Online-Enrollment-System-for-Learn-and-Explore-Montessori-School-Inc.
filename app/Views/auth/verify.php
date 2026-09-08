<div class="auth-shell">
    <div class="auth-panel-form">
        <span class="auth-eyebrow">SmartEnroll · LEMS</span>
        <h1>Check Your Email</h1>
        <p class="auth-subtitle">
            We sent a 6-digit code to <strong><?= htmlspecialchars($email ?: 'your email') ?></strong>.
            Enter it below to activate your account.
        </p>

        <?php if (!empty($error)): ?>
            <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="POST" action="/verify-email">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($email) ?>">

            <label for="code">6-Digit Code</label>
            <input type="text" id="code" name="code" class="code-input"
                   inputmode="numeric" pattern="\d{6}" maxlength="6"
                   autocomplete="one-time-code" autofocus required>

            <button type="submit">Verify Email</button>
        </form>

        <form method="POST" action="/resend-verification" class="resend-form resend-form-inline">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
            <button type="submit" class="btn-clay-ghost-small">Resend code</button>
        </form>

        <p class="auth-footnote">Wrong email? <a href="/register">Register again</a> or <a href="/login">log in</a>.</p>
    </div>

    <div class="auth-panel-art">
        <?php include __DIR__ . '/../partials/clay-illustration.php'; ?>
        <p class="auth-caption">Almost there — just one more step.</p>
    </div>
</div>
