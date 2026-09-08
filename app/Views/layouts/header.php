<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartEnroll — LEMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@500;600;700&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="/" class="brand">
            <svg class="brand-mark" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M24 6C15 6 8 13 8 22c0 9 7 18 16 20 9-2 16-11 16-20 0-9-7-16-16-16Z" fill="var(--clay-accent)"/>
                <path d="M24 14c-3.5 3-5 7-5 11 0 4 1.5 7.5 5 9 3.5-1.5 5-5 5-9 0-4-1.5-8-5-11Z" fill="var(--clay-primary)"/>
            </svg>
            <span>SmartEnroll</span>
        </a>
        <nav>
            <?php if (\App\Middleware\Auth::check()): ?>
                <span class="nav-user"><?= htmlspecialchars(\App\Helpers\Session::get('user_name', '')) ?></span>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
                <a href="/register">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
