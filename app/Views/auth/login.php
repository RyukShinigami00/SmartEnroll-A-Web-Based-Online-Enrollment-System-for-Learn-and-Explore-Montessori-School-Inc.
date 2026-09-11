<div class="auth-shell">
    <div class="auth-panel-form">
        <span class="auth-eyebrow">SmartEnroll · LEMS</span>
        <h1>Welcome Back!</h1>
        <p class="auth-subtitle">Log in to manage enrollment, sections, and schedules.</p>

        <?php if (!empty($error)): ?>
            <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <p class="alert alert-success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <form method="POST" action="/login">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Log In</button>
        </form>

        <p class="auth-footnote">Don't have an account? <a href="/register">Register here</a></p>
    </div>

    <div class="auth-panel-art">
        <?php include __DIR__ . '/../partials/clay-illustration.php'; ?>
        <p class="auth-caption">Growing every learner, one step at a time.</p>
    </div>
</div>
